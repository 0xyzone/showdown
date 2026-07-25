<?php

use App\Models\GameTitle;
use App\Models\Partner;
use App\Models\Sponsor;
use App\Models\Tournament;
use App\Services\ChallongeService;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $activeTournament = Tournament::where('is_active', true)->with('gameTitles')->first() ?? Tournament::with('gameTitles')->first();

    $sponsors = Sponsor::where('is_active', true)
        ->where(function ($query) use ($activeTournament) {
            $query->whereNull('tournament_id');
            if ($activeTournament) {
                $query->orWhere('tournament_id', $activeTournament->id);
            }
        })
        ->orderBy('order')
        ->get();

    $partners = Partner::where('is_active', true)
        ->where(function ($query) use ($activeTournament) {
            $query->whereNull('tournament_id');
            if ($activeTournament) {
                $query->orWhere('tournament_id', $activeTournament->id);
            }
        })
        ->orderBy('order')
        ->get();

    $gameTitles = $activeTournament && $activeTournament->gameTitles->count() > 0
        ? $activeTournament->gameTitles
        : GameTitle::all();

    $challongeService = new ChallongeService;
    $challongeEmbedUrl = $activeTournament?->challonge_embed_url
        ?: ($activeTournament?->challonge_url ? $challongeService->getEmbedUrl($activeTournament->challonge_url) : 'https://challonge.com/module');

    return view('welcome', compact(
        'sponsors',
        'partners',
        'gameTitles',
        'activeTournament',
        'challongeEmbedUrl'
    ));
});

// Preview routes for testing the custom esports error pages
Route::get('/test-error/{code}', function ($code) {
    $validCodes = ['403', '404', '419', '429', '500', '503'];
    if (in_array($code, $validCodes)) {
        return response()->view("errors.{$code}", [], (int) $code);
    }
    abort(404);
});
