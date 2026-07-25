<?php

namespace App\Services;

use App\Models\TournamentRegistration;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DiscordService
{
    public static function sendAnnouncement(?string $webhookUrl, string $title, string $description, int $color = 65280, array $fields = []): bool
    {
        $url = $webhookUrl ?: config('services.discord.webhook_url', env('DISCORD_WEBHOOK_URL'));

        if (empty($url)) {
            return false;
        }

        try {
            $embed = [
                'title' => $title,
                'description' => $description,
                'color' => $color,
                'timestamp' => now()->toIso8601String(),
                'footer' => [
                    'text' => 'Outlaw Showdown 2026 • Official Tournament Bot',
                ],
            ];

            if (! empty($fields)) {
                $embed['fields'] = $fields;
            }

            $response = Http::post($url, [
                'username' => 'Outlaw Esports Bot',
                'avatar_url' => asset('images/sponsor_placeholder.png'),
                'embeds' => [$embed],
            ]);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::error('Discord Webhook Error: '.$e->getMessage());
        }

        return false;
    }

    public static function sendRegistrationNotification(TournamentRegistration $registration, ?string $webhookUrl = null): bool
    {
        $teamName = $registration->team?->name ?? 'Team';
        $tournamentName = $registration->tournament?->name ?? 'Outlaw Showdown';
        $status = strtoupper($registration->status);

        $color = match ($registration->status) {
            'approved' => 65280,    // Green
            'rejected' => 16711680, // Red
            default => 16776960,    // Yellow
        };

        return static::sendAnnouncement(
            $webhookUrl ?: $registration->tournament?->discord_webhook_url,
            "🏆 Registration Alert: {$teamName}",
            "Team **{$teamName}** registration status for **{$tournamentName}** is now **{$status}**.",
            $color,
            [
                ['name' => 'Team Tag', 'value' => $registration->team?->tag ?? 'N/A', 'inline' => true],
                ['name' => 'Manager', 'value' => $registration->registeredBy?->name ?? 'Manager', 'inline' => true],
                ['name' => 'Status', 'value' => $status, 'inline' => true],
            ]
        );
    }
}
