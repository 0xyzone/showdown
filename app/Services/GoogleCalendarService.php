<?php

namespace App\Services;

use App\Models\LeadMeeting;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleCalendarService
{
    protected string $clientId;

    protected string $clientSecret;

    protected string $redirectUri;

    public function __construct()
    {
        $this->clientId = (string) config('services.google.client_id', env('GOOGLE_CLIENT_ID', ''));
        $this->clientSecret = (string) config('services.google.client_secret', env('GOOGLE_CLIENT_SECRET', ''));
        $this->redirectUri = (string) config('services.google.redirect_uri', env('GOOGLE_REDIRECT_URI', ''));
    }

    /**
     * Generate the Google OAuth 2.0 authorization redirect URL.
     */
    public function getAuthUrl(?string $state = null): string
    {
        $scopes = [
            'https://www.googleapis.com/auth/calendar.events',
            'https://www.googleapis.com/auth/userinfo.email',
            'https://www.googleapis.com/auth/userinfo.profile',
            'openid',
        ];

        $params = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'response_type' => 'code',
            'scope' => implode(' ', $scopes),
            'access_type' => 'offline',
            'prompt' => 'consent', // Ensures refresh_token is always returned
        ];

        if ($state) {
            $params['state'] = $state;
        }

        return 'https://accounts.google.com/o/oauth2/v2/auth?'.http_build_query($params);
    }

    /**
     * Exchange authorization code for access & refresh tokens and user info.
     */
    public function handleCallback(string $code): array
    {
        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'code' => $code,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->redirectUri,
            'grant_type' => 'authorization_code',
        ]);

        if (! $response->successful()) {
            Log::error('Google OAuth token exchange failed', ['body' => $response->body()]);
            throw new Exception('Failed to exchange authorization code with Google: '.$response->body());
        }

        $tokenData = $response->json();
        $accessToken = $tokenData['access_token'] ?? null;

        // Fetch user email from Google UserInfo
        $email = null;
        if ($accessToken) {
            $userResponse = Http::withToken($accessToken)->get('https://www.googleapis.com/oauth2/v2/userinfo');
            if ($userResponse->successful()) {
                $email = $userResponse->json('email');
            }
        }

        $expiresIn = (int) ($tokenData['expires_in'] ?? 3600);

        return [
            'access_token' => $accessToken,
            'refresh_token' => $tokenData['refresh_token'] ?? null,
            'expires_at' => now()->addSeconds($expiresIn)->toIso8601String(),
            'email' => $email,
        ];
    }

    /**
     * Ensure a valid access token exists for user, refreshing if expired.
     */
    public function getValidAccessToken(User $user): ?string
    {
        $tokenData = $user->google_calendar_token;
        if (empty($tokenData)) {
            return null;
        }

        $accessToken = $tokenData['access_token'] ?? null;
        $refreshToken = $tokenData['refresh_token'] ?? null;
        $expiresAt = isset($tokenData['expires_at']) ? Carbon::parse($tokenData['expires_at']) : null;

        // If access token is valid and not expired (with 2 min buffer), return it
        if ($accessToken && $expiresAt && $expiresAt->isAfter(now()->addMinutes(2))) {
            return $accessToken;
        }

        // If expired or missing, refresh token
        if (! $refreshToken) {
            return $accessToken;
        }

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'refresh_token' => $refreshToken,
            'grant_type' => 'refresh_token',
        ]);

        if ($response->successful()) {
            $refreshed = $response->json();
            $newAccessToken = $refreshed['access_token'] ?? $accessToken;
            $newExpiresIn = (int) ($refreshed['expires_in'] ?? 3600);

            $tokenData['access_token'] = $newAccessToken;
            $tokenData['expires_at'] = now()->addSeconds($newExpiresIn)->toIso8601String();

            $user->update([
                'google_calendar_token' => $tokenData,
            ]);

            return $newAccessToken;
        }

        Log::error('Failed to refresh Google token', ['body' => $response->body()]);

        return null;
    }

    /**
     * Sync a LeadMeeting to Google Calendar.
     */
    public function syncMeeting(LeadMeeting $meeting): ?string
    {
        $user = $meeting->user ?: ($meeting->lead?->user ?: Auth::user());
        if (! $user) {
            Log::info('Google Calendar Sync Skipped: No user associated with meeting #'.$meeting->id);

            return null;
        }

        if (! $user->isGoogleCalendarConnected()) {
            Log::info('Google Calendar Sync Skipped: User '.$user->email.' (ID: '.$user->id.') has not connected Google Calendar.');

            return null;
        }

        $accessToken = $this->getValidAccessToken($user);
        if (! $accessToken) {
            Log::warning('Google Calendar Sync Skipped: Could not obtain valid access token for user '.$user->email);

            return null;
        }

        $lead = $meeting->lead;
        $description = "Lead: {$lead?->company_name}\nContact: {$lead?->contact_name} ({$lead?->phone}, {$lead?->email})\n";
        if (! empty($meeting->notes)) {
            $description .= "\nNotes: {$meeting->notes}";
        }

        $attendees = [];
        if (! empty($lead?->email)) {
            $attendees[] = [
                'email' => $lead->email,
                'displayName' => $lead->contact_name ?: $lead->company_name,
            ];
        }

        $eventPayload = [
            'summary' => $meeting->title.' - '.($lead?->company_name ?: 'Lead Meeting'),
            'description' => $description,
            'start' => [
                'dateTime' => Carbon::parse($meeting->meeting_start)->toRfc3339String(),
                'timeZone' => config('app.timezone', 'UTC'),
            ],
            'end' => [
                'dateTime' => Carbon::parse($meeting->meeting_end)->toRfc3339String(),
                'timeZone' => config('app.timezone', 'UTC'),
            ],
            'attendees' => $attendees,
        ];

        if ($meeting->meeting_location_type === 'online_meet') {
            $eventPayload['conferenceData'] = [
                'createRequest' => [
                    'requestId' => 'meeting-'.$meeting->id.'-'.time(),
                    'conferenceSolutionKey' => [
                        'type' => 'hangoutsMeet',
                    ],
                ],
            ];
        } elseif ($meeting->meeting_location_type === 'in_person' && ! empty($lead?->address)) {
            $eventPayload['location'] = $lead->address;
        }

        try {
            if ($meeting->google_event_id) {
                // Update existing event
                $url = "https://www.googleapis.com/calendar/v3/calendars/primary/events/{$meeting->google_event_id}?conferenceDataVersion=1";
                $response = Http::withToken($accessToken)->put($url, $eventPayload);
            } else {
                // Create new event
                $url = 'https://www.googleapis.com/calendar/v3/calendars/primary/events?conferenceDataVersion=1';
                $response = Http::withToken($accessToken)->post($url, $eventPayload);
            }

            if ($response->successful()) {
                $createdEvent = $response->json();
                $googleEventId = $createdEvent['id'] ?? null;
                $hangoutLink = $createdEvent['hangoutLink'] ?? ($createdEvent['conferenceData']['entryPoints'][0]['uri'] ?? $meeting->meeting_link);

                $meeting->updateQuietly([
                    'google_event_id' => $googleEventId,
                    'meeting_link' => $hangoutLink ?: $meeting->meeting_link,
                ]);

                Log::info('Google Calendar Event Synced Successfully', [
                    'meeting_id' => $meeting->id,
                    'event_id' => $googleEventId,
                    'hangout_link' => $hangoutLink,
                ]);

                return $googleEventId;
            }

            Log::warning('Google Calendar sync failed response from Google API', [
                'status' => $response->status(),
                'body' => $response->body(),
                'payload' => $eventPayload,
            ]);
        } catch (\Throwable $e) {
            Log::error('Exception during Google Calendar sync: '.$e->getMessage(), [
                'exception' => $e,
            ]);
        }

        return null;
    }

    /**
     * Delete an event from Google Calendar when meeting is deleted.
     */
    public function deleteMeeting(LeadMeeting $meeting): bool
    {
        $user = $meeting->user;
        if (! $user || ! $user->isGoogleCalendarConnected() || ! $meeting->google_event_id) {
            return false;
        }

        $accessToken = $this->getValidAccessToken($user);
        if (! $accessToken) {
            return false;
        }

        try {
            $url = "https://www.googleapis.com/calendar/v3/calendars/primary/events/{$meeting->google_event_id}";
            $response = Http::withToken($accessToken)->delete($url);

            return $response->successful() || $response->status() === 404;
        } catch (\Throwable $e) {
            Log::error('Exception deleting Google Calendar event: '.$e->getMessage());

            return false;
        }
    }
}
