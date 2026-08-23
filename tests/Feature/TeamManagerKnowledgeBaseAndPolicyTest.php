<?php

namespace Tests\Feature;

use App\Models\GameTitle;
use App\Models\Participant;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\TournamentRegistration;
use App\Policies\TeamPolicy;
use App\Policies\TournamentPolicy;
use App\Policies\TournamentRegistrationPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamManagerKnowledgeBaseAndPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_guide_page_renders_successfully(): void
    {
        $response = $this->get('/guide');

        $response->assertStatus(200);
        $response->assertSee('Tournament Registration');
        $response->assertSee('Knowledge Base');
        $response->assertSee('PUBG Mobile');
        $response->assertSee('Mobile Legends: Bang Bang');
        $response->assertSee('Valorant');
    }

    public function test_knowledge_base_url_redirects_to_guide(): void
    {
        $response = $this->get('/knowledge-base');

        $response->assertRedirect(route('guide'));
    }

    public function test_guide_displays_active_tournament_details(): void
    {
        $tournament = Tournament::create([
            'name' => 'Apex Champions 2026',
            'slug' => 'apex-champions-2026',
            'season_version' => 'Vol-II',
            'theme_color' => '#10b981',
            'prize_pool_total' => 500000.00,
            'is_active' => true,
            'status' => 'registration_open',
        ]);

        $game = GameTitle::create([
            'name' => 'PUBG Mobile',
            'slug' => 'pubg-mobile',
            'game_type' => 'battle_royale',
            'min_main_players' => 4,
            'max_substitutes' => 2,
        ]);

        $tournament->gameTitles()->attach($game, [
            'prize_pool' => 200000.00,
        ]);

        $response = $this->get('/guide');

        $response->assertStatus(200);
        $response->assertSee('Apex Champions 2026');
        $response->assertSee('PUBG Mobile');
    }

    public function test_participant_policy_authorizations(): void
    {
        $participant1 = Participant::create([
            'name' => 'Sumin Shrestha',
            'email' => 'sumin@test.com',
            'password' => bcrypt('password'),
            'role' => 'manager',
        ]);

        $participant2 = Participant::create([
            'name' => 'Rohan Thapa',
            'email' => 'rohan@test.com',
            'password' => bcrypt('password'),
            'role' => 'manager',
        ]);

        $team1 = Team::create([
            'manager_id' => $participant1->id,
            'name' => 'Outlaw Strike',
            'tag' => 'OST',
            'country' => 'Nepal',
        ]);

        $team2 = Team::create([
            'manager_id' => $participant2->id,
            'name' => 'Rival Strike',
            'tag' => 'RST',
            'country' => 'Nepal',
        ]);

        $tournament = Tournament::create([
            'name' => 'National Showdown',
            'slug' => 'national-showdown',
            'is_active' => true,
            'status' => 'registration_open',
        ]);

        $reg1 = TournamentRegistration::create([
            'tournament_id' => $tournament->id,
            'team_id' => $team1->id,
            'registered_by' => $participant1->id,
            'status' => 'pending',
            'roster_data' => [],
        ]);

        $reg2 = TournamentRegistration::create([
            'tournament_id' => $tournament->id,
            'team_id' => $team2->id,
            'registered_by' => $participant2->id,
            'status' => 'pending',
            'roster_data' => [],
        ]);

        $teamPolicy = new TeamPolicy;
        $this->assertTrue($teamPolicy->viewAny($participant1));
        $this->assertTrue($teamPolicy->create($participant1));
        $this->assertTrue($teamPolicy->view($participant1, $team1));
        $this->assertFalse($teamPolicy->view($participant1, $team2));
        $this->assertTrue($teamPolicy->update($participant1, $team1));
        $this->assertFalse($teamPolicy->update($participant1, $team2));
        $this->assertTrue($teamPolicy->delete($participant1, $team1));
        $this->assertFalse($teamPolicy->delete($participant1, $team2));

        $tourneyPolicy = new TournamentPolicy;
        $this->assertTrue($tourneyPolicy->viewAny($participant1));
        $this->assertTrue($tourneyPolicy->view($participant1, $tournament));
        $this->assertFalse($tourneyPolicy->create($participant1));

        $regPolicy = new TournamentRegistrationPolicy;
        $this->assertTrue($regPolicy->viewAny($participant1));
        $this->assertTrue($regPolicy->create($participant1));
        $this->assertTrue($regPolicy->view($participant1, $reg1));
        $this->assertFalse($regPolicy->view($participant1, $reg2));
        $this->assertTrue($regPolicy->update($participant1, $reg1));
        $this->assertFalse($regPolicy->update($participant1, $reg2));
    }
}
