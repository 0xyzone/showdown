<?php

use App\Http\Controllers\TicketVerificationController;
use App\Models\Partner;
use App\Models\Sponsor;
use App\Models\Tournament;
use App\Models\TournamentRegistration;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $activeTournament = Tournament::where('is_active', true)
        ->with(['gameTitles'])
        ->first();

    $otherTournaments = Tournament::where('id', '!=', $activeTournament?->id ?? 0)
        ->with(['gameTitles'])
        ->orderBy('start_date', 'asc')
        ->take(4)
        ->get();

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

    $gameTitles = $activeTournament ? $activeTournament->gameTitles : collect();

    // Approved contender teams for the active tournament
    $approvedRegistrations = $activeTournament ? TournamentRegistration::where('tournament_id', $activeTournament->id)
        ->where('status', 'approved')
        ->with(['team.gameTitle', 'registeredBy'])
        ->latest()
        ->take(12)
        ->get() : collect();

    $registrationCount = $activeTournament
        ? TournamentRegistration::where('tournament_id', $activeTournament->id)->count()
        : 0;

    return view('welcome', compact(
        'sponsors',
        'partners',
        'gameTitles',
        'activeTournament',
        'otherTournaments',
        'approvedRegistrations',
        'registrationCount'
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

// Standalone Ticket Verification & Attendee Check-In
Route::get('/ticket/verify/{token?}', [TicketVerificationController::class, 'show'])->name('ticket.verify');
Route::post('/ticket/verify/{token}/check-in', [TicketVerificationController::class, 'markAttended'])->name('ticket.check-in');

// Secure Admin Ticket & Receipt Downloads
Route::get('/admin/ticket-purchases/{purchase}/pdf', [TicketVerificationController::class, 'downloadPdf'])->name('admin.ticket-purchases.pdf');
Route::get('/admin/ticket-purchases/{purchase}/receipt', [TicketVerificationController::class, 'downloadReceipt'])->name('admin.ticket-purchases.receipt');
