<?php

namespace Tests\Feature;

use App\Enums\CampaignPriority;
use App\Enums\CampaignStatus;
use App\Models\Campaign;
use App\Models\CampaignDeliverable;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CampaignResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::first() ?? User::factory()->create([
            'name' => 'Admin Test',
            'email' => 'admin.test@showdown.test',
        ]);

        $role = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $this->adminUser->assignRole($role);
    }

    public function test_campaign_model_can_be_created_with_deliverables(): void
    {
        $campaign = Campaign::create([
            'title' => 'Test Championship Promo',
            'slug' => 'test-championship-promo',
            'campaign_code' => 'CMP-TEST-01',
            'budget' => 50000,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(14)->toDateString(),
            'status' => CampaignStatus::Draft,
            'priority' => CampaignPriority::High,
            'owner_id' => $this->adminUser->id,
        ]);

        $this->assertDatabaseHas('campaigns', [
            'id' => $campaign->id,
            'campaign_code' => 'CMP-TEST-01',
        ]);

        $deliverable = CampaignDeliverable::create([
            'campaign_id' => $campaign->id,
            'title' => 'Test Promo Reel',
            'scheduled_at' => now()->addDays(2),
        ]);

        $this->assertCount(1, $campaign->fresh()->deliverables);
        $this->assertEquals($campaign->id, $deliverable->campaign->id);
    }

    public function test_campaign_routes_are_accessible_by_authenticated_admin(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get('/maidan/campaigns');

        $response->assertSuccessful();

        $kanbanResponse = $this->actingAs($this->adminUser)
            ->get('/maidan/campaigns/view/kanban');

        $kanbanResponse->assertSuccessful();

        $timelineResponse = $this->actingAs($this->adminUser)
            ->get('/maidan/campaigns/view/timeline');

        $timelineResponse->assertSuccessful();

        $calendarResponse = $this->actingAs($this->adminUser)
            ->get('/maidan/campaigns/view/calendar');

        $calendarResponse->assertSuccessful();
    }
}
