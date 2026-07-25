<?php

use App\Models\Partner;
use App\Models\Sponsor;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $sponsors = Sponsor::where('is_active', true)->orderBy('order')->get();
    $partners = Partner::where('is_active', true)->orderBy('order')->get();

    return view('welcome', compact('sponsors', 'partners'));
});

// Preview routes for testing the custom esports error pages
Route::get('/test-error/{code}', function ($code) {
    $validCodes = ['403', '404', '419', '429', '500', '503'];
    if (in_array($code, $validCodes)) {
        return response()->view("errors.{$code}", [], (int) $code);
    }
    abort(404);
});
