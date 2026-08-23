<?php

namespace App\Http\Controllers;

use App\Services\GoogleCalendarService;
use Filament\Notifications\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GoogleCalendarOAuthController extends Controller
{
    public function __construct(
        protected GoogleCalendarService $calendarService,
    ) {}

    /**
     * Redirect user to Google OAuth consent screen.
     */
    public function redirect(Request $request): RedirectResponse
    {
        $clientId = config('services.google.client_id', env('GOOGLE_CLIENT_ID'));
        $returnUrl = $request->query('return_url') ?: url()->previous();

        if (empty($clientId)) {
            Notification::make()
                ->title('Google API Credentials Missing')
                ->body('GOOGLE_CLIENT_ID is not configured in your .env file yet. Please set up GOOGLE_CLIENT_ID and GOOGLE_CLIENT_SECRET in .env.')
                ->warning()
                ->send();

            return redirect()->to($returnUrl);
        }

        $state = Str::random(40);
        $request->session()->put('google_oauth_state', $state);
        $request->session()->put('google_oauth_return_url', $returnUrl);

        $authUrl = $this->calendarService->getAuthUrl($state);

        return redirect()->away($authUrl);
    }

    /**
     * Handle OAuth callback from Google.
     */
    public function callback(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $returnUrl = $request->session()->pull('google_oauth_return_url') ?: route('filament.maidan.resources.leads.index');

        if ($request->has('error')) {
            Notification::make()
                ->title('Google Calendar Connection Failed')
                ->body('Authorization was cancelled or denied: '.$request->get('error'))
                ->danger()
                ->send();

            return redirect()->to($returnUrl);
        }

        $code = $request->get('code');
        if (! $code) {
            Notification::make()
                ->title('Invalid Request')
                ->body('No authorization code provided by Google.')
                ->danger()
                ->send();

            return redirect()->to($returnUrl);
        }

        try {
            $tokenData = $this->calendarService->handleCallback($code);

            if ($user) {
                $user->update([
                    'google_calendar_token' => $tokenData,
                    'google_calendar_connected_at' => now(),
                ]);

                Notification::make()
                    ->title('Google Calendar Connected!')
                    ->body('Your Google Calendar ('.($tokenData['email'] ?? 'Google Account').') has been linked successfully. Scheduled meetings will now sync automatically.')
                    ->success()
                    ->send();
            }
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Google Calendar Connection Error')
                ->body('Error linking account: '.$e->getMessage())
                ->danger()
                ->send();
        }

        return redirect()->to($returnUrl);
    }

    /**
     * Disconnect Google Calendar from user account.
     */
    public function disconnect(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $returnUrl = $request->query('return_url') ?: url()->previous();

        if ($user) {
            $user->update([
                'google_calendar_token' => null,
                'google_calendar_connected_at' => null,
            ]);

            Notification::make()
                ->title('Google Calendar Disconnected')
                ->body('Your Google Calendar has been unlinked.')
                ->info()
                ->send();
        }

        return redirect()->to($returnUrl);
    }
}
