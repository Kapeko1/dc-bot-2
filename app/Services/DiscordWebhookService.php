<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class DiscordWebhookService
{
    private string $killsWebhookUrl;
    private string $deathsWebhookUrl;

    public function __construct()
    {
        $this->killsWebhookUrl = config('services.discord.webhook_kills');
        $this->deathsWebhookUrl = config('services.discord.webhook_deaths');
    }

    public function sendKillAlert(array $kill, ?string $inventoryImage = null): void
    {
        $killer = $kill['Killer']['Name'] ?? 'Unknown';
        $victim = $kill['Victim']['Name'] ?? 'Unknown';
        $killArea = $this->formatKillArea($kill['KillArea'] ?? 'UNKNOWN');
        $killId = $kill['EventId'];
        $time = substr($kill['TimeStamp'], 0, 19) . 'Z';

        $killboardUrl = "https://albiononline.com/killboard/kill/{$killId}?server=live_ams";

        $embed = [
            'title' => '⚔️ New Kill Alert!',
            'color' => 0xFF4444,
            'url' => $killboardUrl,
            'timestamp' => $time,
            'fields' => [
                ['name' => '🗡️ Killer', 'value' => $killer, 'inline' => true],
                ['name' => '💀 Victim', 'value' => $victim, 'inline' => true],
                ['name' => '📍 Location', 'value' => $killArea, 'inline' => false],
                ['name' => '🔗 Killboard', 'value' => "[View Details]({$killboardUrl})", 'inline' => false],
            ],
            'footer' => ['text' => "Kill ID: {$killId}"],
        ];

        if ($inventoryImage) {
            $embed['image'] = ['url' => 'attachment://inventory.png'];

            Http::attach(
                'files[0]',
                base64_decode($inventoryImage),
                'inventory.png'
            )->post($this->killsWebhookUrl, [
                'payload_json' => json_encode(['embeds' => [$embed]])
            ]);
        } else {
            Http::post($this->killsWebhookUrl, ['embeds' => [$embed]]);
        }
    }

    public function sendDeathAlert(array $death, ?string $inventoryImage = null): void
    {
        $killer = $death['Killer']['Name'] ?? 'Unknown';
        $victim = $death['Victim']['Name'] ?? 'Unknown';
        $killArea = $this->formatKillArea($death['KillArea'] ?? 'UNKNOWN');
        $deathId = $death['EventId'];
        $time = substr($death['TimeStamp'], 0, 19) . 'Z';

        $killboardUrl = "https://albiononline.com/killboard/kill/{$deathId}?server=live_ams";

        $embed = [
            'title' => '💀 Player Death Alert!',
            'color' => 0x808080,
            'url' => $killboardUrl,
            'timestamp' => $time,
            'fields' => [
                ['name' => '💀 Victim', 'value' => $victim, 'inline' => true],
                ['name' => '🗡️ Killer', 'value' => $killer, 'inline' => true],
                ['name' => '📍 Location', 'value' => $killArea, 'inline' => false],
                ['name' => '🔗 Killboard', 'value' => "[View Details]({$killboardUrl})", 'inline' => false],
            ],
            'footer' => ['text' => "Death ID: {$deathId}"],
        ];

        if ($inventoryImage) {
            $embed['image'] = ['url' => 'attachment://inventory.png'];

            Http::attach(
                'files[0]',
                base64_decode($inventoryImage),
                'inventory.png'
            )->post($this->deathsWebhookUrl, [
                'payload_json' => json_encode(['embeds' => [$embed]])
            ]);
        } else {
            Http::post($this->deathsWebhookUrl, ['embeds' => [$embed]]);
        }
    }

    private function formatKillArea(string $killArea): string
    {
        return match($killArea) {
            'OPEN_WORLD' => 'Open World',
            'CORRUPTED' => 'Corrupted Dungeon',
            'CORRUPTED_DUNGEON' => 'Corrupted Dungeon',
            'HELLGATE' => 'Hellgate',
            'ARENA' => 'Crystal Arena',
            'MISTS' => 'Mists',
            'UNKNOWN' => 'Unknown',
            default => ucwords(str_replace('_', ' ', strtolower($killArea))),
        };
    }
}
