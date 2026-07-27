<?php

use App\Models\Partner;
use App\Models\Sponsor;
use App\Models\Tournament;
use App\Services\ChallongeService;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $activeTournament = Tournament::where('is_active', true)->with('gameTitles')->first();

    $sponsors = Sponsor::where('is_active', true)
        ->where(function ($query) use ($activeTournament) {
            $query->whereNull('tournament_id');
            if ($activeTournament) {
                $query->orWhere('tournament_id', $activeTournament->id);
            }
        })
        ->orderBy('order')
        ->get()
        ->groupBy(function ($sponsor) {
            return strtolower($sponsor->level ?: 'general');
        });

    $partners = Partner::where('is_active', true)
        ->where(function ($query) use ($activeTournament) {
            $query->whereNull('tournament_id');
            if ($activeTournament) {
                $query->orWhere('tournament_id', $activeTournament->id);
            }
        })
        ->orderBy('order')
        ->get()
        ->groupBy(function ($partner) {
            return strtolower($partner->level ?: 'official');
        });

    // Strictly load only the game titles linked to the active tournament
    $gameTitles = $activeTournament ? $activeTournament->gameTitles : collect();

    $challongeService = new ChallongeService;
    $gameChallongeEmbeds = [];

    foreach ($gameTitles as $game) {
        $rawChallonge = $game->pivot?->challonge_url;
        $items = [];

        if (is_array($rawChallonge)) {
            $items = $rawChallonge;
        } elseif (is_string($rawChallonge) && ! empty($rawChallonge)) {
            $decoded = json_decode($rawChallonge, true);
            if (is_array($decoded)) {
                $items = $decoded;
            } else {
                $items = ['Official Bracket' => $rawChallonge];
            }
        }

        $parsedLinks = [];
        foreach ($items as $label => $url) {
            if (empty($url)) {
                continue;
            }
            $embed = str_contains($url, '/module') ? $url : $challongeService->getEmbedUrl($url);
            $parsedLinks[] = [
                'label' => is_numeric($label) ? 'Bracket' : $label,
                'url' => $url,
                'embed_url' => $embed,
            ];
        }

        $gameChallongeEmbeds[$game->id] = $parsedLinks;
    }

    return view('welcome', compact(
        'sponsors',
        'partners',
        'gameTitles',
        'activeTournament',
        'gameChallongeEmbeds'
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
