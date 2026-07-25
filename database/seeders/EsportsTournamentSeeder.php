<?php

namespace Database\Seeders;

use App\Models\GameTitle;
use App\Models\Participant;
use App\Models\Team;
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

        // 2. Create Primary Tournament & Attach Multi-Games
        $tournament = Tournament::firstOrCreate(['slug' => 'outlaw-showdown-2026-vol-1'], [
            'name' => 'Outlaw Showdown 2026 Vol-I',
            'season_version' => '2026 Vol-I',
            'description' => 'Nepal premier national esports championship featuring Rs 500,000 Total Prize Pool across 6 major gaming disciplines.',
            'status' => 'registration_open',
            'is_active' => true,
            'prize_pool_total' => 500000.00,
            'registration_start' => now()->subDays(5),
            'registration_end' => now()->addDays(14),
            'start_date' => now()->addDays(15),
            'end_date' => now()->addDays(20),
            'challonge_url' => 'https://challonge.com/outlaw_showdown_2026',
            'challonge_embed_url' => 'https://challonge.com/outlaw_showdown_2026/module',
            'discord_server_url' => 'https://discord.gg/outlawshowdown',
            'rules_doc_link' => 'https://outlawshowdown.com/rules.pdf',
        ]);

        $tournament->gameTitles()->syncWithoutDetaching([
            $pubg->id,
            $mlbbOpen->id,
            $mlbbWomens->id,
            $efootball->id,
            $valorant->id,
            $cosplay->id,
        ]);

        // 3. Create Sample Participant & Teams
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
            'tag' => 'OTL',
            'country' => 'Nepal',
        ]);

        // 4. Create Registration
        TournamentRegistration::firstOrCreate([
            'tournament_id' => $tournament->id,
            'team_id' => $team1->id,
        ], [
            'registered_by' => $participant->id,
            'status' => 'approved',
            'notes' => 'Roster verified & payment confirmed.',
        ]);
    }
}
