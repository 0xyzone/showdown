<?php

namespace Tests\Feature;

use App\Models\GameTitle;
use App\Models\Participant;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\TournamentRegistration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ViewRegistrationApproveDenyTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_approve_and_deny_registration(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@outlaw.com',
            'password' => Hash::make('password'),
        ]);

        $participant = Participant::create([
            'name' => 'Manager Aarav',
            'email' => 'aarav@outlaw.com',
            'password' => Hash::make('password'),
        ]);

        $gameTitle = GameTitle::create([
            'name' => 'MLBB 5v5',
            'slug' => 'mlbb-approve-test',
        ]);

        $tournament = Tournament::create([
            'name' => 'Outlaw Championship',
            'slug' => 'outlaw-championship-test',
        ]);

        $team = Team::create([
            'manager_id' => $participant->id,
            'game_title_id' => $gameTitle->id,
            'name' => 'Cyber Vipers',
            'tag' => 'CV',
        ]);

        $registration = TournamentRegistration::create([
            'tournament_id' => $tournament->id,
            'team_id' => $team->id,
            'registered_by' => $participant->id,
            'status' => 'pending',
        ]);

        $this->actingAs($admin);

        // Test Approve
        $registration->update(['status' => 'approved', 'notes' => 'Roster and payment verified']);
        $this->assertEquals('approved', $registration->fresh()->status);
        $this->assertEquals('Roster and payment verified', $registration->fresh()->notes);

        // Test Deny
        $registration->update(['status' => 'rejected', 'notes' => 'Invalid payment receipt screenshot']);
        $this->assertEquals('rejected', $registration->fresh()->status);
        $this->assertEquals('Invalid payment receipt screenshot', $registration->fresh()->notes);
    }
}
