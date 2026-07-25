<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChallongeService
{
    protected string $apiKey;

    protected string $subdomain;

    public function __construct()
    {
        $this->apiKey = config('services.challonge.api_key', env('CHALLONGE_API_KEY', ''));
        $this->subdomain = config('services.challonge.subdomain', env('CHALLONGE_SUBDOMAIN', ''));
    }

    public function getEmbedUrl(string $urlOrSlug): string
    {
        if (empty($urlOrSlug)) {
            return '';
        }

        // Clean URL if full challonge URL is passed e.g. https://challonge.com/outlaw_showdown_2026
        $path = parse_url($urlOrSlug, PHP_URL_PATH);
        $slug = trim($path ?? $urlOrSlug, '/');

        if ($this->subdomain && ! str_contains($slug, '-')) {
            return "https://challonge.com/{$this->subdomain}-{$slug}/module";
        }

        return "https://challonge.com/{$slug}/module";
    }

    public function createTournament(string $name, string $urlSlug, string $tournamentType = 'single elimination'): ?array
    {
        if (empty($this->apiKey)) {
            return null;
        }

        try {
            $params = [
                'api_key' => $this->apiKey,
                'tournament[name]' => $name,
                'tournament[url]' => $urlSlug,
                'tournament[tournament_type]' => $tournamentType,
            ];

            if ($this->subdomain) {
                $params['tournament[subdomain]'] = $this->subdomain;
            }

            $response = Http::post('https://api.challonge.com/v1/tournaments.json', $params);

            if ($response->successful()) {
                return $response->json('tournament');
            }
        } catch (\Throwable $e) {
            Log::error('Challonge API Error: '.$e->getMessage());
        }

        return null;
    }
}
