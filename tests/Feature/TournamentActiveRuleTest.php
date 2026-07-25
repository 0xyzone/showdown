<?php

namespace Tests\Feature;

use App\Models\GameTitle;
use App\Models\Tournament;
use App\Services\ChallongeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TournamentActiveRuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_one_tournament_is_active_at_a_time(): void
    {
        $game = GameTitle::create(['name' => 'PUBG Mobile', 'slug' => 'pubg-mobile', 'game_type' => 'battle_royale']);

        $t1 = Tournament::create([
            'name' => 'Tournament 1',
            'slug' => 'tournament-1',
            'is_active' => true,
        ]);
        $t1->gameTitles()->attach($game);

        $this->assertTrue($t1->fresh()->is_active);

        $t2 = Tournament::create([
            'name' => 'Tournament 2',
            'slug' => 'tournament-2',
            'is_active' => true,
        ]);
        $t2->gameTitles()->attach($game);

        $this->assertTrue($t2->fresh()->is_active);
        $this->assertFalse($t1->fresh()->is_active);
        $this->assertEquals(1, Tournament::where('is_active', true)->count());
    }

    public function test_at_least_one_tournament_remains_active(): void
    {
        $game = GameTitle::create(['name' => 'PUBG Mobile', 'slug' => 'pubg-mobile', 'game_type' => 'battle_royale']);

        $t1 = Tournament::create([
            'name' => 'Sole Tournament',
            'slug' => 'sole-tournament',
            'is_active' => true,
        ]);
        $t1->gameTitles()->attach($game);

        $t1->update(['is_active' => false]);

        $this->assertTrue($t1->fresh()->is_active);
    }

    public function test_challonge_service_embed_url(): void
    {
        $service = new ChallongeService;
        $url = $service->getEmbedUrl('https://challonge.com/outlaw_showdown_2026');

        $this->assertEquals('https://challonge.com/outlaw_showdown_2026/module', $url);
    }
}
