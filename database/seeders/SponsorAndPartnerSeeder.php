<?php

namespace Database\Seeders;

use App\Models\Partner;
use App\Models\Sponsor;
use App\Models\Tournament;
use Illuminate\Database\Seeder;

class SponsorAndPartnerSeeder extends Seeder
{
    public function run(): void
    {
        $tournament = Tournament::where('is_active', true)->first() ?? Tournament::first();
        $tournamentId = $tournament?->id;

        // OFFICIAL SPONSORS WITH LEVEL HIERARCHY
        $sponsors = [
            [
                'name' => 'Monster Energy',
                'level' => 'title',
                'website_url' => 'https://monsterenergy.com',
                'tournament_id' => $tournamentId,
                'order' => 1,
            ],
            [
                'name' => 'Razer Gaming',
                'level' => 'platinum',
                'website_url' => 'https://razer.com',
                'tournament_id' => $tournamentId,
                'order' => 2,
            ],
            [
                'name' => 'Logitech G',
                'level' => 'gold',
                'website_url' => 'https://logitechg.com',
                'tournament_id' => $tournamentId,
                'order' => 3,
            ],
            [
                'name' => 'Red Bull Esports',
                'level' => 'gold',
                'website_url' => 'https://redbull.com',
                'tournament_id' => null,
                'order' => 4,
            ],
            [
                'name' => 'Nvidia GeForce',
                'level' => 'silver',
                'website_url' => 'https://nvidia.com',
                'tournament_id' => $tournamentId,
                'order' => 5,
            ],
            [
                'name' => 'HyperX Alfa',
                'level' => 'silver',
                'website_url' => 'https://hyperx.com',
                'tournament_id' => null,
                'order' => 6,
            ],
        ];

        foreach ($sponsors as $s) {
            Sponsor::updateOrCreate(['name' => $s['name']], [
                'tournament_id' => $s['tournament_id'],
                'level' => $s['level'],
                'website_url' => $s['website_url'],
                'order' => $s['order'],
                'is_active' => true,
            ]);
        }

        // OFFICIAL PARTNERS WITH SPECIFIC TITLE CATEGORIES
        $partners = [
            [
                'name' => 'Kantipur Television Network',
                'title' => 'Broadcasting Partner',
                'level' => 'major',
                'website_url' => 'https://kantipurtv.com',
                'tournament_id' => $tournamentId,
                'order' => 1,
            ],
            [
                'name' => 'Hotel Annapurna',
                'title' => 'Hospitality Partner',
                'level' => 'major',
                'website_url' => 'https://annapurna.com',
                'tournament_id' => $tournamentId,
                'order' => 2,
            ],
            [
                'name' => 'Routine of Nepal Banda',
                'title' => 'Media & Digital Partner',
                'level' => 'major',
                'website_url' => 'https://ronb.com',
                'tournament_id' => $tournamentId,
                'order' => 3,
            ],
            [
                'name' => 'Khalti Digital Wallet',
                'title' => 'Official Ticketing Partner',
                'level' => 'major',
                'website_url' => 'https://khalti.com',
                'tournament_id' => $tournamentId,
                'order' => 4,
            ],
            [
                'name' => 'Secretlab Gaming Chairs',
                'title' => 'Official Equipment Partner',
                'level' => 'standard',
                'website_url' => 'https://secretlab.co',
                'tournament_id' => null,
                'order' => 5,
            ],
        ];

        foreach ($partners as $p) {
            Partner::updateOrCreate(['name' => $p['name']], [
                'tournament_id' => $p['tournament_id'],
                'title' => $p['title'],
                'level' => $p['level'],
                'website_url' => $p['website_url'],
                'order' => $p['order'],
                'is_active' => true,
            ]);
        }
    }
}
