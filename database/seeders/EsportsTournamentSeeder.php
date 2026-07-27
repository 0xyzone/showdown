<?php

namespace Database\Seeders;

use App\Models\GameTitle;
use App\Models\Participant;
use App\Models\Team;
use App\Models\TeamPlayer;
use App\Models\Tournament;
use App\Models\TournamentRegistration;
use Illuminate\Database\Seeder;

class EsportsTournamentSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Game Titles
        $pubg = GameTitle::firstOrCreate(['slug' => 'pubg-mobile'], [
            'name' => 'PUBG Mobile',
            'developer' => 'KRAFTON & Tencent Games',
            'game_type' => 'battle_royale',
        ]);

        $mlbbOpen = GameTitle::firstOrCreate(['slug' => 'mlbb-open'], [
            'name' => 'Mobile Legends: Bang Bang (Open)',
            'developer' => 'MOONTON Games',
            'game_type' => '5v5_moba',
        ]);

        $mlbbWomens = GameTitle::firstOrCreate(['slug' => 'mlbb-womens'], [
            'name' => "Mobile Legends: Bang Bang (Women's)",
            'developer' => 'MOONTON Games',
            'game_type' => '5v5_moba',
        ]);

        $efootball = GameTitle::firstOrCreate(['slug' => 'efootball-mobile'], [
            'name' => 'eFootball Mobile',
            'developer' => 'Konami',
            'game_type' => '1v1',
        ]);

        $valorant = GameTitle::firstOrCreate(['slug' => 'valorant'], [
            'name' => 'Valorant',
            'developer' => 'Riot Games',
            'game_type' => 'fps',
        ]);

        $cosplay = GameTitle::firstOrCreate(['slug' => 'cosplay-showdown'], [
            'name' => 'Cyber Cosplay Championship',
            'developer' => 'Outlaw Esports',
            'game_type' => '1v1',
        ]);

        // 2. Create Tournament 1: Primary Featured Active Event
        $t1 = Tournament::firstOrCreate(['slug' => 'outlaw-showdown-2026-vol-1'], [
            'name' => 'Outlaw Showdown 2026 Vol-I',
            'season_version' => '2026 Vol-I',
            'hero_headline' => 'UNLEASH THE LEGEND, CLAIM YOUR GLORY',
            'hero_subheadline' => "Nepal's premier national esports championship stage is live! Register your squad for multi-game title disciplines.",
            'description' => 'Nepal premier national esports championship featuring Rs 500,000 Total Prize Pool across 6 major gaming disciplines.',
            'status' => 'registration_open',
            'is_active' => true,
            'prize_pool_total' => 500000.00,
            'entry_fee' => 100.00,
            'theme_color' => '#10b981',
            'min_main_players' => 5,
            'max_main_players' => 5,
            'max_substitutes' => 2,
            'require_coach' => false,
            'require_manager' => false,
            'registration_start' => now()->subDays(5),
            'registration_end' => now()->addDays(14),
            'start_date' => now()->addDays(15),
            'end_date' => now()->addDays(20),
            'discord_server_url' => 'https://discord.gg/outlawshowdown',
            'rules_doc_link' => 'https://outlawshowdown.com/rules.pdf',
        ]);

        $t1->update([
            'hero_headline' => 'UNLEASH THE LEGEND, CLAIM YOUR GLORY',
            'hero_subheadline' => "Nepal's premier national esports championship stage is live! Register your squad for multi-game title disciplines and follow live brackets on Challonge.com.",
            'entry_fee' => 100.00,
            'is_active' => true,
            'min_main_players' => 5,
            'max_main_players' => 5,
            'max_substitutes' => 2,
        ]);

        $t1->gameTitles()->syncWithoutDetaching([
            $pubg->id => [
                'prize_pool' => 150000.00,
                'prize_distribution' => json_encode(['1st Place' => '80000', '2nd Place' => '45000', '3rd Place' => '25000']),
                'challonge_url' => json_encode(['Group A' => 'https://challonge.com/outlaw_pubg_group_a', 'Grand Finals' => 'https://challonge.com/outlaw_pubg_finals']),
            ],
            $mlbbOpen->id => [
                'prize_pool' => 120000.00,
                'prize_distribution' => json_encode(['1st Place' => '65000', '2nd Place' => '35000', '3rd Place' => '20000']),
                'challonge_url' => json_encode(['Playoffs' => 'https://challonge.com/outlaw_mlbb_playoffs']),
            ],
            $mlbbWomens->id => [
                'prize_pool' => 80000.00,
                'prize_distribution' => json_encode(['1st Place' => '45000', '2nd Place' => '25000', '3rd Place' => '10000']),
                'challonge_url' => json_encode(['Main Bracket' => 'https://challonge.com/outlaw_mlbb_women']),
            ],
            $efootball->id => [
                'prize_pool' => 50000.00,
                'prize_distribution' => json_encode(['1st Place' => '30000', '2nd Place' => '15000', '3rd Place' => '5000']),
                'challonge_url' => json_encode(['Swiss Stage' => 'https://challonge.com/outlaw_efootball_swiss']),
            ],
            $valorant->id => [
                'prize_pool' => 80000.00,
                'prize_distribution' => json_encode(['1st Place' => '45000', '2nd Place' => '25000', '3rd Place' => '10000']),
                'challonge_url' => json_encode(['Knockout Stage' => 'https://challonge.com/outlaw_valorant_ko']),
            ],
            $cosplay->id => [
                'prize_pool' => 20000.00,
                'prize_distribution' => json_encode(['Best Cosplayer' => '12000', 'Runner-up' => '8000']),
                'challonge_url' => null,
            ],
        ]);

        // 3. Create Tournament 2: Secondary Upcoming Event
        $t2 = Tournament::firstOrCreate(['slug' => 'outlaw-winter-showdown-2026'], [
            'name' => 'Outlaw Winter Showdown 2026',
            'season_version' => '2026 Winter Ed.',
            'hero_headline' => 'WINTER ARENA CHAMPIONSHIP 2026',
            'hero_subheadline' => 'The battleground shifts to the winter arena! Top tactical FPS and mobile legends assemble for the ultimate winter trophy.',
            'description' => 'Winter arena edition for top mobile and tactical FPS contenders in South Asia.',
            'status' => 'draft',
            'is_active' => false,
            'prize_pool_total' => 250000.00,
            'entry_fee' => 150.00,
            'theme_color' => '#06b6d4',
            'min_main_players' => 5,
            'max_main_players' => 5,
            'max_substitutes' => 2,
            'registration_start' => now()->addDays(30),
            'registration_end' => now()->addDays(45),
            'start_date' => now()->addDays(46),
            'end_date' => now()->addDays(50),
            'discord_server_url' => 'https://discord.gg/outlawshowdown',
            'rules_doc_link' => 'https://outlawshowdown.com/rules-winter.pdf',
        ]);

        $t2->gameTitles()->syncWithoutDetaching([
            $pubg->id,
            $valorant->id,
            $mlbbOpen->id,
        ]);

        // 4. Create Sample Participants & Teams
        $participant = Participant::firstOrCreate(['email' => 'player@outlaw.com'], [
            'name' => 'Aarav Sharma',
            'password' => bcrypt('password'),
            'phone' => '+9779801234567',
            'ign' => 'OUTLAW_Viper',
            'discord_tag' => 'Viper#1337',
            'role' => 'manager',
            'bio' => 'Competitive Esports Manager & IGL for Outlaw Clan.',
        ]);

        $team1 = Team::firstOrCreate(['name' => 'Outlaw Esports'], [
            'manager_id' => $participant->id,
            'game_title_id' => $mlbbOpen->id,
            'tag' => 'OTL',
            'country' => 'Nepal',
        ]);

        $team2 = Team::firstOrCreate(['name' => 'Elementrix Esports'], [
            'manager_id' => $participant->id,
            'game_title_id' => $valorant->id,
            'tag' => 'ELMX',
            'country' => 'Nepal',
        ]);

        // 5. Create Sample Players for Team 1
        $playerNames = ['Rohan Gurung', 'Bikash Thapa', 'Sujan Shrestha', 'Ayush Rai', 'Nabin Karki', 'Kiran Lama', 'Aashish Neupane'];
        $roles = ['main_player', 'main_player', 'main_player', 'main_player', 'main_player', 'substitute', 'substitute'];
        $ingameRoles = ['Jungler', 'Exp Laner', 'Mid Laner', 'Gold Laner', 'Roamer', 'Flex', 'Reserve'];

        foreach ($playerNames as $index => $name) {
            TeamPlayer::firstOrCreate([
                'team_id' => $team1->id,
                'full_name' => $name,
            ], [
                'role' => $roles[$index],
                'date_of_birth' => '2002-05-15',
                'ign' => 'OTL_'.str_replace(' ', '_', $name),
                'ingame_role' => $ingameRoles[$index],
                'whatsapp_number' => '+977981122334'.$index,
                'email' => strtolower(str_replace(' ', '.', $name)).'@gmail.com',
                'discord_id' => str_replace(' ', '', $name).'#1234',
                'citizenship_number' => '12-01-78-0432'.$index,
            ]);
        }

        // 6. Create Tournament Registrations
        TournamentRegistration::firstOrCreate([
            'tournament_id' => $t1->id,
            'team_id' => $team1->id,
        ], [
            'registered_by' => $participant->id,
            'status' => 'approved',
            'notes' => 'Roster verified & payment confirmed.',
        ]);

        TournamentRegistration::firstOrCreate([
            'tournament_id' => $t1->id,
            'team_id' => $team2->id,
        ], [
            'registered_by' => $participant->id,
            'status' => 'pending',
            'notes' => 'Payment receipt under verification.',
        ]);
    }
}
