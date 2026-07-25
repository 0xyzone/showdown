<?php

namespace Tests\Feature;

use App\Livewire\SponsorQueryForm;
use App\Mail\SponsorQueryConverted;
use App\Mail\SponsorQueryReceived;
use App\Models\Partner;
use App\Models\Sponsor;
use App\Models\SponsorQuery;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class SponsorManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_loads_dynamic_sponsors_and_partners(): void
    {
        Sponsor::create([
            'name' => 'Apex Tech',
            'level' => 'title',
            'is_active' => true,
        ]);

        Partner::create([
            'name' => 'Kantipur TV',
            'title' => 'Broadcasting Partner',
            'level' => 'major',
            'is_active' => true,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Apex Tech');
        $response->assertSee('Kantipur TV');
        $response->assertSee('Broadcasting Partner');
    }

    public function test_user_can_submit_sponsor_query_and_receives_confirmation_email(): void
    {
        Mail::fake();

        Livewire::test(SponsorQueryForm::class)
            ->fill([
                'name' => 'John Doe',
                'company_name' => 'Cyber Gaming Co',
                'email' => 'john@cybergaming.com',
                'phone' => '+9779800000000',
                'details' => 'We want to sponsor the event as gold sponsor.',
            ])
            ->call('submit')
            ->assertSet('isSubmitted', true);

        $this->assertDatabaseHas(SponsorQuery::class, [
            'company_name' => 'Cyber Gaming Co',
            'email' => 'john@cybergaming.com',
            'status' => 'pending',
        ]);

        Mail::assertSent(SponsorQueryReceived::class, function ($mail) {
            return $mail->hasTo('john@cybergaming.com');
        });
    }

    public function test_admin_can_convert_query_and_dispatches_welcome_email(): void
    {
        Mail::fake();

        $admin = User::factory()->create();

        $query = SponsorQuery::create([
            'name' => 'Jane Smith',
            'company_name' => 'Redline Gaming',
            'email' => 'jane@redline.com',
            'phone' => '+9779811111111',
            'details' => 'Platinum sponsorship query.',
            'status' => 'pending',
        ]);

        $this->actingAs($admin);

        $sponsor = Sponsor::create([
            'name' => $query->company_name,
            'level' => 'platinum',
            'sponsor_query_id' => $query->id,
            'is_active' => true,
        ]);

        $query->update([
            'status' => 'converted',
            'converted_type' => Sponsor::class,
            'converted_id' => $sponsor->id,
        ]);

        Mail::to($query->email)->send(new SponsorQueryConverted($query, 'Official Sponsor', 'PLATINUM Sponsor Tier'));

        $this->assertDatabaseHas(Sponsor::class, [
            'name' => 'Redline Gaming',
            'level' => 'platinum',
            'sponsor_query_id' => $query->id,
        ]);

        Mail::assertSent(SponsorQueryConverted::class, function ($mail) {
            return $mail->hasTo('jane@redline.com');
        });
    }
}
