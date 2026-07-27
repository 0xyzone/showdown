<?php

namespace Database\Seeders;

use App\Models\GameTitle;
use Illuminate\Database\Seeder;

class GameTitleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gameTitles = [
            [
                'name' => 'PUBG Mobile',
                'slug' => 'pubg-mobile',
                'developer' => 'Krafton / Tencent Games',
                'logo_path' => 'game_titles/pubg_mobile.png',
                'banner_path' => null,
                'game_type' => 'battle_royale',
                'min_main_players' => 4,
                'max_substitutes' => 2,
            ],
            [
                'name' => 'Valorant',
                'slug' => 'valorant',
                'developer' => 'Riot Games',
                'logo_path' => 'game_titles/valorant.png',
                'banner_path' => null,
                'game_type' => 'tactical_shooter',
                'min_main_players' => 5,
                'max_substitutes' => 2,
            ],
            [
                'name' => 'Mobile Legends: Bang Bang',
                'slug' => 'mobile-legends-bang-bang',
                'developer' => 'Moonton',
                'logo_path' => 'game_titles/mobile_legends.png',
                'banner_path' => null,
                'game_type' => 'moba',
                'min_main_players' => 5,
                'max_substitutes' => 2,
            ],
            [
                'name' => 'Free Fire',
                'slug' => 'free-fire',
                'developer' => 'Garena',
                'logo_path' => 'game_titles/free_fire.png',
                'banner_path' => null,
                'game_type' => 'battle_royale',
                'min_main_players' => 4,
                'max_substitutes' => 2,
            ],
        ];

        foreach ($gameTitles as $data) {
            GameTitle::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }
    }
}
