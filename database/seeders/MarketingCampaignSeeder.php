<?php

namespace Database\Seeders;

use App\Enums\CampaignPriority;
use App\Enums\CampaignStatus;
use App\Enums\DeliverableApprovalStatus;
use App\Enums\DeliverableType;
use App\Enums\MarketingPlatform;
use App\Models\Campaign;
use App\Models\CampaignDeliverable;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class MarketingCampaignSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first() ?? User::factory()->create([
            'name' => 'Marketing Lead',
            'email' => 'marketing@showdown.test',
        ]);

        $campaignsData = [
            [
                'title' => 'Nepal Esports Championship 2026 Grand Reveal',
                'slug' => 'nepal-esports-championship-2026-grand-reveal',
                'campaign_code' => 'CMP-NEC26-01',
                'objectives' => 'Build massive pre-registration hype for the flagship tournament across college gaming networks and Twitch/YouTube gaming communities.',
                'target_audience' => 'South Asian competitive gamers aged 16-28 interested in Valorant, MLBB, and PUBG Mobile.',
                'budget' => 250000.00,
                'actual_spend' => 110000.00,
                'start_date' => Carbon::now()->subDays(5)->toDateString(),
                'end_date' => Carbon::now()->addDays(20)->toDateString(),
                'status' => CampaignStatus::Running,
                'priority' => CampaignPriority::Urgent,
                'platforms' => [
                    MarketingPlatform::Instagram->value,
                    MarketingPlatform::TikTok->value,
                    MarketingPlatform::YouTube->value,
                    MarketingPlatform::Facebook->value,
                ],
                'tags' => ['Tournament Hype', 'Flagship Event', 'Sponsorship Push'],
                'owner_id' => $admin->id,
                'deliverables' => [
                    [
                        'title' => 'Cinematic Teaser Reel - The Arena Awaits',
                        'creative_type' => DeliverableType::Reels,
                        'platform' => MarketingPlatform::Instagram,
                        'copy_text' => "Nepal's biggest gaming arena returns. Are you ready to take the crown? 🏆 Register now link in bio! #Showdown2026 #GamingNepal",
                        'designer_notes' => 'High-tempo cuts, heavy bass drop on the date reveal.',
                        'scheduled_at' => Carbon::now()->addDays(1)->setTime(18, 0),
                        'approval_status' => DeliverableApprovalStatus::Approved,
                        'spend' => 15000.00,
                        'impressions' => 45000,
                        'reach' => 38000,
                    ],
                    [
                        'title' => 'Tournament Prize Pool Infographic Carousel',
                        'creative_type' => DeliverableType::Carousels,
                        'platform' => MarketingPlatform::Instagram,
                        'copy_text' => 'Rs. 2,500,000 Total Prize Pool Breakdown! Swipe to check prize allocation by game.',
                        'designer_notes' => 'Gold/black esports brand palette with modern typography.',
                        'scheduled_at' => Carbon::now()->addDays(4)->setTime(16, 30),
                        'approval_status' => DeliverableApprovalStatus::PendingReview,
                        'spend' => 8000.00,
                    ],
                    [
                        'title' => 'TikTok Viral Gameplay Hype Montage',
                        'creative_type' => DeliverableType::MotionGraphics,
                        'platform' => MarketingPlatform::TikTok,
                        'copy_text' => 'POV: You clutch 1v4 in the final round 🔥 Tag your duo partner!',
                        'designer_notes' => 'Vertical 9:16 format with trending audio.',
                        'scheduled_at' => Carbon::now()->addDays(6)->setTime(19, 0),
                        'approval_status' => DeliverableApprovalStatus::Approved,
                        'spend' => 12000.00,
                    ],
                ],
            ],
            [
                'title' => 'Early Bird Ticket Sales Launch',
                'slug' => 'early-bird-ticket-sales-launch',
                'campaign_code' => 'CMP-TKT-EB01',
                'objectives' => 'Sell out 500 VIP and Gold audience passes in first 72 hours with an exclusive early bird discount.',
                'target_audience' => 'Spectators, anime/cosplay community, university students.',
                'budget' => 80000.00,
                'actual_spend' => 25000.00,
                'start_date' => Carbon::now()->subDays(2)->toDateString(),
                'end_date' => Carbon::now()->addDays(10)->toDateString(),
                'status' => CampaignStatus::InProduction,
                'priority' => CampaignPriority::High,
                'platforms' => [
                    MarketingPlatform::Facebook->value,
                    MarketingPlatform::Instagram->value,
                    MarketingPlatform::GoogleAds->value,
                ],
                'tags' => ['Ticket Sales', 'VIP Access', 'Discounts'],
                'owner_id' => $admin->id,
                'deliverables' => [
                    [
                        'title' => 'VIP Benefits Showcase Static Banner',
                        'creative_type' => DeliverableType::StaticGraphics2D,
                        'platform' => MarketingPlatform::Facebook,
                        'copy_text' => 'Front row seats, backstage pass, and exclusive merchandise gift pack!',
                        'designer_notes' => 'Include QR code and booking button.',
                        'scheduled_at' => Carbon::now()->addDays(2)->setTime(14, 0),
                        'approval_status' => DeliverableApprovalStatus::NeedsRevisions,
                        'spend' => 5000.00,
                    ],
                ],
            ],
            [
                'title' => 'Brand Sponsor Spotlight Series',
                'slug' => 'brand-sponsor-spotlight-series',
                'campaign_code' => 'CMP-SPON-26',
                'objectives' => 'Highlight beverage, hardware, and telco title sponsors with co-branded social content.',
                'target_audience' => 'Tech enthusiasts and gamers.',
                'budget' => 120000.00,
                'actual_spend' => 0.00,
                'start_date' => Carbon::now()->addDays(8)->toDateString(),
                'end_date' => Carbon::now()->addDays(28)->toDateString(),
                'status' => CampaignStatus::Scheduled,
                'priority' => CampaignPriority::Medium,
                'platforms' => [
                    MarketingPlatform::LinkedIn->value,
                    MarketingPlatform::YouTube->value,
                    MarketingPlatform::TwitterX->value,
                ],
                'tags' => ['Sponsorship', 'B2B', 'Tech Hardware'],
                'owner_id' => $admin->id,
                'deliverables' => [
                    [
                        'title' => 'Official Gaming Hardware Partner Announcement',
                        'creative_type' => DeliverableType::Blogs,
                        'platform' => MarketingPlatform::LinkedIn,
                        'copy_text' => 'Proud to announce ASUS ROG as the official rig sponsor of Showdown 2026!',
                        'designer_notes' => 'Corporate esports tone, co-branded logo guidelines.',
                        'scheduled_at' => Carbon::now()->addDays(9)->setTime(11, 0),
                        'approval_status' => DeliverableApprovalStatus::PendingReview,
                        'spend' => 10000.00,
                    ],
                ],
            ],
        ];

        foreach ($campaignsData as $data) {
            $deliverables = $data['deliverables'] ?? [];
            unset($data['deliverables']);

            $campaign = Campaign::firstOrCreate(
                ['campaign_code' => $data['campaign_code']],
                $data
            );

            foreach ($deliverables as $deliv) {
                $deliv['campaign_id'] = $campaign->id;
                $deliv['assigned_to'] = $admin->id;
                CampaignDeliverable::create($deliv);
            }
        }
    }
}
