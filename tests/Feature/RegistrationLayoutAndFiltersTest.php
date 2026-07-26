<?php

namespace Tests\Feature;

use App\Models\GameTitle;
use App\Models\Participant;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\TournamentRegistration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegistrationLayoutAndFiltersTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_fetches_game_title_from_team_and_filters(): void
    {
        $participant = Participant::create([
            'name' => 'Manager Aarav',
            'email' => 'aarav@showdown.com',
            'password' => Hash::make('password'),
        ]);

        $gameTitle = GameTitle::create([
            'name' => 'MLBB 5v5',
            'slug' => 'mlbb-5v5-filter',
            'game_type' => '5v5_moba',
        ]);

        $tournament = Tournament::create([
            'name' => 'Showdown Major 2026',
            'slug' => 'showdown-major-2026',
            'status' => 'registration_open',
        ]);

        $team = Team::create([
            'manager_id' => $participant->id,
            'game_title_id' => $gameTitle->id,
            'name' => 'Outlaw Squad',
            'tag' => 'OTL',
        ]);

        $registration = TournamentRegistration::create([
            'tournament_id' => $tournament->id,
            'team_id' => $team->id,
            'registered_by' => $participant->id,
            'status' => 'approved',
        ]);

        $this->assertEquals('MLBB 5v5', $registration->team->gameTitle->name);
        $this->assertEquals('Showdown Major 2026', $registration->tournament->name);
    }
}
