<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AlbionApiService
{
    private string $baseUrl = 'https://gameinfo-ams.albiononline.com/api/gameinfo';

    public function getPlayerKills(string $playerId): array
    {
        $response = Http::get("{$this->baseUrl}/players/{$playerId}/kills");

        if (!$response->ok()) {
            return [];
        }

        $kills = $response->json();

        usort($kills, fn($a, $b) =>
            strtotime($b['TimeStamp']) <=> strtotime($a['TimeStamp'])
        );

        return $kills;
    }

    public function getPlayerDeaths(string $playerId): array
    {
        $response = Http::get("{$this->baseUrl}/players/{$playerId}/deaths");

        if (!$response->ok()) {
            return [];
        }

        $deaths = $response->json();

        usort($deaths, fn($a, $b) =>
            strtotime($b['TimeStamp']) <=> strtotime($a['TimeStamp'])
        );

        return $deaths;
    }
}
