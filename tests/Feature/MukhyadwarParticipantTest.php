<?php

namespace Tests\Feature;

use App\Models\Participant;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MukhyadwarParticipantTest extends TestCase
{
    use RefreshDatabase;

    public function test_participant_can_access_mukhyadwar_panel(): void
    {
        $participant = Participant::create([
            'name' => 'John Player',
            'email' => 'john@player.com',
            'password' => bcrypt('password'),
            'role' => 'manager',
        ]);

        $response = $this->actingAs($participant, 'participant')
            ->get('/mukhyadwar');

        $response->assertStatus(200);
    }

    public function test_participant_can_create_team(): void
    {
        $participant = Participant::create([
            'name' => 'Jane Leader',
            'email' => 'jane@leader.com',
            'password' => bcrypt('password'),
            'role' => 'manager',
        ]);

        $team = Team::create([
            'manager_id' => $participant->id,
            'name' => 'Alpha Clan',
            'tag' => 'ACL',
            'country' => 'Nepal',
        ]);

        $this->assertDatabaseHas('teams', [
            'name' => 'Alpha Clan',
            'manager_id' => $participant->id,
        ]);
    }
}
