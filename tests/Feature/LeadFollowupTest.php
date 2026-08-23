<?php

namespace Tests\Feature;

use App\Filament\Resources\Leads\Pages\ListLeads;
use App\Models\Lead;
use App\Models\LeadFollowup;
use App\Models\LeadStatus;
use App\Models\LeadType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LeadFollowupTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_lead_with_followups(): void
    {
        $user = User::factory()->create();
        $type = LeadType::factory()->create();
        $status = LeadStatus::factory()->create();

        $lead = Lead::factory()->create([
            'user_id' => $user->id,
            'lead_type_id' => $type->id,
            'lead_status_id' => $status->id,
            'company_name' => 'Acme Esports',
            'contact_name' => 'John Doe',
            'phone' => '9800000000',
            'email' => 'john@acme.com',
        ]);

        $followup1 = LeadFollowup::factory()->create([
            'lead_id' => $lead->id,
            'user_id' => $user->id,
            'followup_date' => '2026-08-20',
            'remarks' => 'Initial discovery call with marketing team.',
        ]);

        $followup2 = LeadFollowup::factory()->create([
            'lead_id' => $lead->id,
            'user_id' => $user->id,
            'followup_date' => '2026-08-22',
            'remarks' => 'Sent customized tournament sponsorship proposal.',
        ]);

        $this->assertCount(2, $lead->fresh()->followups);
        $this->assertEquals($followup2->id, $lead->fresh()->latestFollowup->id);
        $this->assertEquals('Sent customized tournament sponsorship proposal.', $lead->fresh()->latestFollowup->remarks);
    }

    public function test_followup_belongs_to_lead_and_user(): void
    {
        $user = User::factory()->create(['name' => 'Agent Alex']);
        $lead = Lead::factory()->create(['company_name' => 'Apex Gaming']);

        $followup = LeadFollowup::factory()->create([
            'lead_id' => $lead->id,
            'user_id' => $user->id,
            'followup_date' => '2026-08-23',
            'remarks' => 'Confirmed meeting for next Tuesday.',
        ]);

        $this->assertEquals('Apex Gaming', $followup->lead->company_name);
        $this->assertEquals('Agent Alex', $followup->user->name);
        $this->assertInstanceOf(Carbon::class, $followup->followup_date);
    }

    public function test_deleting_lead_cascades_and_deletes_its_followups(): void
    {
        $lead = Lead::factory()->create();
        $followup = LeadFollowup::factory()->create([
            'lead_id' => $lead->id,
        ]);

        $this->assertDatabaseHas('lead_followups', ['id' => $followup->id]);

        $lead->delete();

        $this->assertDatabaseMissing('lead_followups', ['id' => $followup->id]);
    }

    public function test_filament_can_list_and_render_lead_table_with_followup_columns(): void
    {
        Role::create(['name' => 'super_admin']);
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        $lead = Lead::factory()->create(['company_name' => 'TechCorp Gaming']);
        LeadFollowup::factory()->create([
            'lead_id' => $lead->id,
            'user_id' => $admin->id,
            'followup_date' => '2026-08-23',
            'remarks' => 'Partnership meeting held.',
        ]);

        Livewire::actingAs($admin)
            ->test(ListLeads::class)
            ->assertCanSeeTableRecords([$lead]);
    }
}
