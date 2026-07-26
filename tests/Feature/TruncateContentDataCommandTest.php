<?php

namespace Tests\Feature;

use App\Models\Participant;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TruncateContentDataCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_db_truncate_content_command_clears_content_and_preserves_users(): void
    {
        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@outlaw.com',
            'password' => Hash::make('password'),
        ]);

        $tournament = Tournament::create([
            'name' => 'Test Tournament',
            'slug' => 'test-tournament-truncate',
        ]);

        $participant = Participant::create([
            'name' => 'Test Participant',
            'email' => 'participant@test.com',
            'password' => Hash::make('password'),
        ]);

        $team = Team::create([
            'name' => 'Test Team',
            'tag' => 'TT',
        ]);

        $this->assertDatabaseHas('users', ['id' => $user->id]);
        $this->assertDatabaseHas('tournaments', ['id' => $tournament->id]);
        $this->assertDatabaseHas('participants', ['id' => $participant->id]);
        $this->assertDatabaseHas('teams', ['id' => $team->id]);

        $this->artisan('db:truncate-content', ['--force' => true])
            ->assertExitCode(0);

        // Verify users preserved
        $this->assertDatabaseHas('users', ['id' => $user->id]);

        // Verify content tables truncated
        $this->assertDatabaseMissing('tournaments', ['id' => $tournament->id]);
        $this->assertDatabaseMissing('participants', ['id' => $participant->id]);
        $this->assertDatabaseMissing('teams', ['id' => $team->id]);
    }
}
