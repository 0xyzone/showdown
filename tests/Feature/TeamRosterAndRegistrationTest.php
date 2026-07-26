<?php

namespace Tests\Feature;

use App\Filament\Mukhyadwar\Resources\TournamentResource;
use App\Models\GameTitle;
use App\Models\Participant;
use App\Models\Team;
use App\Models\TeamPlayer;
use App\Models\Tournament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TeamRosterAndRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_create_team_with_players_and_game_title(): void
    {
        $participant = Participant::create([
            'name' => 'Manager Aarav',
            'email' => 'aarav@outlaw.com',
            'password' => Hash::make('password'),
        ]);

        $gameTitle = GameTitle::create([
            'name' => 'Mobile Legends',
            'slug' => 'mlbb-test',
            'game_type' => '5v5_moba',
            'min_main_players' => 5,
            'max_substitutes' => 2,
        ]);

        $team = Team::create([
            'manager_id' => $participant->id,
            'game_title_id' => $gameTitle->id,
            'name' => 'Apex Squad',
            'tag' => 'APX',
            'country' => 'Nepal',
        ]);

        $player = TeamPlayer::create([
            'team_id' => $team->id,
            'full_name' => 'Bikash Thapa',
            'role' => 'main_player',
            'date_of_birth' => '2001-01-01',
            'ign' => 'APX_Bikash',
            'ingame_role' => 'Jungler',
            'whatsapp_number' => '+9779800000000',
            'email' => 'bikash@apx.com',
            'discord_id' => 'Bikash#1234',
            'citizenship_number' => '123456789',
        ]);

        $this->assertDatabaseHas('teams', [
            'id' => $team->id,
            'name' => 'Apex Squad',
            'game_title_id' => $gameTitle->id,
        ]);

        $this->assertDatabaseHas('team_players', [
            'id' => $player->id,
            'full_name' => 'Bikash Thapa',
            'ign' => 'APX_Bikash',
        ]);
    }

    public function test_game_title_roster_rules_validation(): void
    {
        $participant = Participant::create([
            'name' => 'Manager Aarav',
            'email' => 'aarav2@outlaw.com',
            'password' => Hash::make('password'),
        ]);

        $gameTitle = GameTitle::create([
            'name' => 'MLBB 5v5',
            'slug' => 'mlbb-5v5',
            'game_type' => '5v5_moba',
            'min_main_players' => 5,
            'max_substitutes' => 2,
        ]);

        $tournament = Tournament::create([
            'name' => 'Showdown League 2026',
            'slug' => 'showdown-league-2026',
            'status' => 'registration_open',
            'is_active' => true,
            'entry_fee' => 100,
        ]);

        $team = Team::create([
            'manager_id' => $participant->id,
            'game_title_id' => $gameTitle->id,
            'name' => 'Team Solo',
            'tag' => 'TSO',
        ]);

        // Create only 3 main players (less than 5 required for MLBB)
        for ($i = 1; $i <= 3; $i++) {
            TeamPlayer::create([
                'team_id' => $team->id,
                'full_name' => "Player $i",
                'role' => 'main_player',
                'ign' => "IGN_$i",
            ]);
        }

        // Processing registration should fail due to incomplete roster for game title
        TournamentResource::processTeamRegistration([
            'team_id' => $team->id,
            'selected_players' => $team->players->pluck('id')->toArray(),
            'payment_receipt_path' => 'receipts/test.png',
        ], $tournament);

        $this->assertDatabaseMissing('tournament_registrations', [
            'tournament_id' => $tournament->id,
            'team_id' => $team->id,
        ]);
    }
}
