<?php

namespace Database\Seeders;

use App\Models\GameTitle;
use App\Models\Participant;
use App\Models\Team;
use App\Models\TeamPlayer;
use App\Models\Tournament;
use App\Models\TournamentRegistration;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummyRegistrationSeeder extends Seeder
{
    public function run(): void
    {
        // 0. Ensure Admin User
        User::firstOrCreate(['email' => 'admin@outlaw.com'], [
            'name' => 'Outlaw Admin',
            'password' => Hash::make('password'),
        ]);

        // 1. Fetch Game Titles & Tournaments
        $mlbb = GameTitle::where('slug', 'mlbb-open')->first() ?? GameTitle::firstOrCreate(['slug' => 'mlbb-open'], ['name' => 'Mobile Legends: Bang Bang (Open)', 'game_type' => '5v5_moba', 'min_main_players' => 5, 'max_substitutes' => 2]);
        $valorant = GameTitle::where('slug', 'valorant')->first() ?? GameTitle::firstOrCreate(['slug' => 'valorant'], ['name' => 'Valorant', 'game_type' => 'fps', 'min_main_players' => 5, 'max_substitutes' => 2]);
        $pubg = GameTitle::where('slug', 'pubg-mobile')->first() ?? GameTitle::firstOrCreate(['slug' => 'pubg-mobile'], ['name' => 'PUBG Mobile', 'game_type' => 'battle_royale', 'min_main_players' => 4, 'max_substitutes' => 2]);
        $efootball = GameTitle::where('slug', 'efootball-mobile')->first() ?? GameTitle::firstOrCreate(['slug' => 'efootball-mobile'], ['name' => 'eFootball Mobile', 'game_type' => '1v1', 'min_main_players' => 1, 'max_substitutes' => 1]);

        $tournament = Tournament::where('is_active', true)->first() ?? Tournament::where('status', 'registration_open')->first();

        // 2. Create Participant Managers
        $m1 = Participant::firstOrCreate(['email' => 'player@outlaw.com'], [
            'name' => 'Aarav Sharma',
            'password' => Hash::make('password'),
            'phone' => '+977 9801234567',
            'ign' => 'OTL_Aarav',
            'discord_tag' => 'Aarav#1337',
            'role' => 'manager',
            'bio' => 'Esports Team Director & Manager for Outlaw Esports.',
        ]);

        $m2 = Participant::firstOrCreate(['email' => 'manager2@showdown.com'], [
            'name' => 'Kripa Shrestha',
            'password' => Hash::make('password'),
            'phone' => '+977 9812345678',
            'ign' => 'DRG_Kripa',
            'discord_tag' => 'Kripa#2026',
            'role' => 'manager',
            'bio' => 'General Manager for Cyber Dragons and Neon Vipers.',
        ]);

        $m3 = Participant::firstOrCreate(['email' => 'manager3@showdown.com'], [
            'name' => 'Suman Gurung',
            'password' => Hash::make('password'),
            'phone' => '+977 9823456789',
            'ign' => 'VALK_Suman',
            'discord_tag' => 'Suman#9999',
            'role' => 'manager',
            'bio' => 'Team Owner & Captain for Valkyrie Gaming.',
        ]);

        // 3. Create Esports Teams
        $teamsData = [
            [
                'name' => 'Outlaw Esports',
                'tag' => 'OTL',
                'manager' => $m1,
                'game' => $mlbb,
                'registered' => true,
                'reg_status' => 'approved',
                'players' => [
                    ['full_name' => 'Rohan Gurung', 'role' => 'main_player', 'ign' => 'OTL_Rohan', 'ingame_role' => 'Jungler', 'dob' => '2002-04-12'],
                    ['full_name' => 'Bikash Thapa', 'role' => 'main_player', 'ign' => 'OTL_Bikash', 'ingame_role' => 'Exp Laner', 'dob' => '2001-08-20'],
                    ['full_name' => 'Sujan Shrestha', 'role' => 'main_player', 'ign' => 'OTL_Sujan', 'ingame_role' => 'Mid Laner', 'dob' => '2003-01-15'],
                    ['full_name' => 'Ayush Rai', 'role' => 'main_player', 'ign' => 'OTL_Ayush', 'ingame_role' => 'Gold Laner', 'dob' => '2002-11-05'],
                    ['full_name' => 'Nabin Karki', 'role' => 'main_player', 'ign' => 'OTL_Nabin', 'ingame_role' => 'Roamer', 'dob' => '2000-09-30'],
                    ['full_name' => 'Kiran Lama', 'role' => 'substitute', 'ign' => 'OTL_Kiran', 'ingame_role' => 'Flex Sub', 'dob' => '2004-03-18'],
                    ['full_name' => 'Aashish Neupane', 'role' => 'substitute', 'ign' => 'OTL_Aashish', 'ingame_role' => 'Reserve', 'dob' => '2003-07-22'],
                    ['full_name' => 'Dipesh Basnet', 'role' => 'coach', 'ign' => 'OTL_CoachDipesh', 'ingame_role' => 'Head Coach', 'dob' => '1996-05-10'],
                ],
            ],
            [
                'name' => 'Elementrix Esports',
                'tag' => 'ELMX',
                'manager' => $m1,
                'game' => $valorant,
                'registered' => true,
                'reg_status' => 'pending',
                'players' => [
                    ['full_name' => 'Samir Adhikari', 'role' => 'main_player', 'ign' => 'ELMX_Samir', 'ingame_role' => 'Duelist / Jett', 'dob' => '2002-06-14'],
                    ['full_name' => 'Prashant Maharjan', 'role' => 'main_player', 'ign' => 'ELMX_Prashant', 'ingame_role' => 'Initiator / Sova', 'dob' => '2001-09-19'],
                    ['full_name' => 'Nirajan KC', 'role' => 'main_player', 'ign' => 'ELMX_Nirajan', 'ingame_role' => 'Controller / Omen', 'dob' => '2003-02-28'],
                    ['full_name' => 'Saurav Gautam', 'role' => 'main_player', 'ign' => 'ELMX_Saurav', 'ingame_role' => 'Sentinel / Killjoy', 'dob' => '2002-12-10'],
                    ['full_name' => 'Kushal Ale', 'role' => 'main_player', 'ign' => 'ELMX_Kushal', 'ingame_role' => 'Flex / IGL', 'dob' => '2000-11-25'],
                    ['full_name' => 'Roshan Tamang', 'role' => 'substitute', 'ign' => 'ELMX_Roshan', 'ingame_role' => 'Sub Controller', 'dob' => '2004-05-04'],
                ],
            ],
            [
                'name' => 'Cyber Dragons',
                'tag' => 'DRG',
                'manager' => $m2,
                'game' => $pubg,
                'registered' => false,
                'reg_status' => null,
                'players' => [
                    ['full_name' => 'Anish Khadka', 'role' => 'main_player', 'ign' => 'DRG_Anish', 'ingame_role' => 'Assaulter', 'dob' => '2001-03-21'],
                    ['full_name' => 'Ramesh Giri', 'role' => 'main_player', 'ign' => 'DRG_Ramesh', 'ingame_role' => 'Sniper', 'dob' => '2002-07-11'],
                    ['full_name' => 'Prakash Joshi', 'role' => 'main_player', 'ign' => 'DRG_Prakash', 'ingame_role' => 'IGL', 'dob' => '2000-10-08'],
                    ['full_name' => 'Subash Chettri', 'role' => 'main_player', 'ign' => 'DRG_Subash', 'ingame_role' => 'Support', 'dob' => '2003-04-16'],
                    ['full_name' => 'Manoj Rijal', 'role' => 'substitute', 'ign' => 'DRG_Manoj', 'ingame_role' => 'Reserve Assaulter', 'dob' => '2004-01-30'],
                ],
            ],
            [
                'name' => 'Neon Vipers',
                'tag' => 'VPR',
                'manager' => $m2,
                'game' => $mlbb,
                'registered' => false,
                'reg_status' => null,
                'players' => [
                    ['full_name' => 'Saroj Dahal', 'role' => 'main_player', 'ign' => 'VPR_Saroj', 'ingame_role' => 'Jungler', 'dob' => '2002-08-08'],
                    ['full_name' => 'Bijay Bista', 'role' => 'main_player', 'ign' => 'VPR_Bijay', 'ingame_role' => 'Exp Laner', 'dob' => '2001-12-12'],
                    ['full_name' => 'Dinesh Bhatta', 'role' => 'main_player', 'ign' => 'VPR_Dinesh', 'ingame_role' => 'Mid Laner', 'dob' => '2003-05-20'],
                    ['full_name' => 'Hari Prasad', 'role' => 'main_player', 'ign' => 'VPR_Hari', 'ingame_role' => 'Gold Laner', 'dob' => '2002-02-14'],
                    ['full_name' => 'Kamal Devkota', 'role' => 'main_player', 'ign' => 'VPR_Kamal', 'ingame_role' => 'Roamer', 'dob' => '2000-06-25'],
                ],
            ],
            [
                'name' => 'Valkyrie Gaming',
                'tag' => 'VALK',
                'manager' => $m3,
                'game' => $valorant,
                'registered' => false,
                'reg_status' => null,
                'players' => [
                    ['full_name' => 'Shruti Poudel', 'role' => 'main_player', 'ign' => 'VALK_Shruti', 'ingame_role' => 'Duelist / Raze', 'dob' => '2003-03-03'],
                    ['full_name' => 'Kabita Sharma', 'role' => 'main_player', 'ign' => 'VALK_Kabita', 'ingame_role' => 'Initiator / Fade', 'dob' => '2002-09-17'],
                    ['full_name' => 'Pooja Magar', 'role' => 'main_player', 'ign' => 'VALK_Pooja', 'ingame_role' => 'Controller / Viper', 'dob' => '2001-07-29'],
                    ['full_name' => 'Riya Shrestha', 'role' => 'main_player', 'ign' => 'VALK_Riya', 'ingame_role' => 'Sentinel / Cypher', 'dob' => '2003-10-14'],
                    ['full_name' => 'Sneha Karki', 'role' => 'main_player', 'ign' => 'VALK_Sneha', 'ingame_role' => 'Flex', 'dob' => '2002-05-01'],
                ],
            ],
            [
                'name' => 'Royal Knights',
                'tag' => 'RKN',
                'manager' => $m3,
                'game' => $efootball,
                'registered' => false,
                'reg_status' => null,
                'players' => [
                    ['full_name' => 'Suman Gurung', 'role' => 'main_player', 'ign' => 'RKN_SumanPro', 'ingame_role' => 'Solo Athlete', 'dob' => '2000-04-18'],
                    ['full_name' => 'Sandesh Karki', 'role' => 'substitute', 'ign' => 'RKN_Sandesh', 'ingame_role' => 'Backup Athlete', 'dob' => '2003-08-22'],
                ],
            ],
        ];

        foreach ($teamsData as $data) {
            $team = Team::firstOrCreate([
                'name' => $data['name'],
            ], [
                'manager_id' => $data['manager']->id,
                'game_title_id' => $data['game']->id,
                'tag' => $data['tag'],
                'country' => 'Nepal',
            ]);

            foreach ($data['players'] as $pData) {
                TeamPlayer::firstOrCreate([
                    'team_id' => $team->id,
                    'full_name' => $pData['full_name'],
                ], [
                    'role' => $pData['role'],
                    'date_of_birth' => $pData['dob'],
                    'ign' => $pData['ign'],
                    'ingame_role' => $pData['ingame_role'],
                    'whatsapp_number' => '+977 98'.rand(10000000, 99999999),
                    'email' => strtolower(str_replace(' ', '.', $pData['full_name'])).'@gmail.com',
                    'discord_id' => str_replace(' ', '', $pData['full_name']).'#'.rand(1000, 9999),
                    'citizenship_number' => '12-01-78-'.rand(10000, 99999),
                ]);
            }

            if ($data['registered'] && $tournament) {
                $rosterPayload = $team->players->map(fn ($p) => [
                    'id' => $p->id,
                    'name' => $p->full_name,
                    'role' => $p->role,
                    'ign' => $p->ign,
                    'ingame_role' => $p->ingame_role,
                ])->values()->toArray();

                TournamentRegistration::firstOrCreate([
                    'tournament_id' => $tournament->id,
                    'team_id' => $team->id,
                ], [
                    'registered_by' => $data['manager']->id,
                    'status' => $data['reg_status'],
                    'roster_data' => $rosterPayload,
                    'notes' => "Dummy seeder registration for {$team->name}. Status: {$data['reg_status']}.",
                ]);
            }
        }
    }
}
