<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\LeadMeeting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class LeadMeetingTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_lead_with_meetings(): void
    {
        $user = User::factory()->create();
        $lead = Lead::factory()->create(['user_id' => $user->id]);

        $meeting = LeadMeeting::factory()->create([
            'lead_id' => $lead->id,
            'user_id' => $user->id,
            'title' => 'Esports Sponsorship Briefing',
            'meeting_start' => now()->addDay()->setHour(10)->setMinute(0),
            'meeting_end' => now()->addDay()->setHour(11)->setMinute(0),
            'meeting_location_type' => 'online_meet',
            'meeting_link' => 'https://meet.google.com/xyz-abcd-efg',
            'status' => 'scheduled',
        ]);

        $this->assertCount(1, $lead->fresh()->meetings);
        $this->assertEquals('Esports Sponsorship Briefing', $lead->fresh()->meetings->first()->title);
        $this->assertInstanceOf(Carbon::class, $meeting->meeting_start);
        $this->assertEquals('https://meet.google.com/xyz-abcd-efg', $meeting->meeting_link);
    }

    public function test_deleting_lead_cascades_and_deletes_meetings(): void
    {
        $lead = Lead::factory()->create();
        $meeting = LeadMeeting::factory()->create(['lead_id' => $lead->id]);

        $this->assertDatabaseHas('lead_meetings', ['id' => $meeting->id]);

        $lead->delete();

        $this->assertDatabaseMissing('lead_meetings', ['id' => $meeting->id]);
    }

    public function test_user_google_calendar_connection_helpers(): void
    {
        $user = User::factory()->create();
        $this->assertFalse($user->isGoogleCalendarConnected());
        $this->assertNull($user->getGoogleCalendarEmail());

        $user->update([
            'google_calendar_token' => [
                'access_token' => 'mock-access-token',
                'refresh_token' => 'mock-refresh-token',
                'expires_at' => now()->addHour()->toIso8601String(),
                'email' => 'staff@showdown.gg',
            ],
            'google_calendar_connected_at' => now(),
        ]);

        $this->assertTrue($user->fresh()->isGoogleCalendarConnected());
        $this->assertEquals('staff@showdown.gg', $user->fresh()->getGoogleCalendarEmail());
    }

    public function test_google_calendar_service_syncs_meeting_with_google_api(): void
    {
        Http::fake([
            'https://www.googleapis.com/calendar/v3/calendars/primary/events?conferenceDataVersion=1' => Http::response([
                'id' => 'mock_google_event_123',
                'hangoutLink' => 'https://meet.google.com/abc-defg-hij',
            ], 200),
        ]);

        $user = User::factory()->create([
            'google_calendar_token' => [
                'access_token' => 'valid-token',
                'refresh_token' => 'valid-refresh',
                'expires_at' => now()->addHours(2)->toIso8601String(),
                'email' => 'admin@gmail.com',
            ],
            'google_calendar_connected_at' => now(),
        ]);

        $lead = Lead::factory()->create(['company_name' => 'Red Bull Gaming', 'email' => 'sponsor@redbull.com']);

        $meeting = LeadMeeting::create([
            'lead_id' => $lead->id,
            'user_id' => $user->id,
            'title' => 'Pitch Red Bull Stage',
            'meeting_start' => now()->addDays(2),
            'meeting_end' => now()->addDays(2)->addHour(),
            'meeting_location_type' => 'online_meet',
        ]);

        $this->assertEquals('mock_google_event_123', $meeting->fresh()->google_event_id);
        $this->assertEquals('https://meet.google.com/abc-defg-hij', $meeting->fresh()->meeting_link);
    }

    public function test_google_calendar_oauth_redirect_and_disconnect(): void
    {
        config(['services.google.client_id' => 'mock-google-client-id.apps.googleusercontent.com']);

        $user = User::factory()->create([
            'google_calendar_token' => ['access_token' => 'test'],
        ]);

        $response = $this->actingAs($user)->get(route('google.calendar.redirect'));
        $response->assertRedirect();
        $this->assertStringContainsString('accounts.google.com', $response->headers->get('Location'));

        $disconnectResponse = $this->actingAs($user)->get(route('google.calendar.disconnect'));
        $disconnectResponse->assertRedirect();
        $this->assertFalse($user->fresh()->isGoogleCalendarConnected());
    }
}
