<?php

namespace Tests\Feature;

use App\Models\GameTitle;
use App\Models\Partner;
use App\Models\Sponsor;
use App\Models\Tournament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomepageRedesignTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_renders_successfully_with_active_tournament_and_dynamic_theme(): void
    {
        $tournament = Tournament::create([
            'name' => 'Nexus Championship 2026',
            'slug' => 'nexus-championship-2026',
            'season_version' => 'Season 1',
            'hero_headline' => 'DOMINATE THE APEX',
            'hero_subheadline' => 'Compete with the top squads.',
            'theme_color' => '#8b5cf6',
            'prize_pool_total' => 350000.00,
            'entry_fee' => 200.00,
            'entry_fee_suffix' => 'team',
            'is_active' => true,
            'status' => 'registration_open',
            'start_date' => now()->addDays(10),
            'registration_end' => now()->addDays(5),
        ]);

        $game = GameTitle::create([
            'name' => 'Valorant Prime',
            'slug' => 'valorant-prime',
            'game_type' => 'fps',
        ]);

        $tournament->gameTitles()->attach($game, [
            'prize_pool' => 150000.00,
            'prize_distribution' => json_encode(['1st Place' => '100000', '2nd Place' => '50000']),
        ]);

        $sponsor = Sponsor::create([
            'name' => 'HyperX Nepal',
            'level' => 'title',
            'is_active' => true,
            'order' => 1,
            'tournament_id' => $tournament->id,
        ]);

        $partner = Partner::create([
            'name' => 'Red Bull Gaming',
            'level' => 'official',
            'is_active' => true,
            'order' => 1,
            'tournament_id' => $tournament->id,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Nexus Championship 2026');
        $response->assertSee('DOMINATE THE APEX');
        $response->assertSee('--primary: #8b5cf6', false);
        $response->assertSee('Valorant Prime');
        $response->assertSee('HyperX Nepal');
        $response->assertSee('Red Bull Gaming');
        $response->assertDontSee('challonge', false);
        $response->assertDontSee('Challonge');
    }

    public function test_homepage_renders_when_no_active_tournament_exists(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Next Championship Series In Preparation');
        $response->assertDontSee('Challonge');
    }
}
