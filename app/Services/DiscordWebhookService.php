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

    public function sendKillAlert(array $kill): void
    {
        $killer = $kill['Killer']['Name'] ?? 'Unknown';
        $victim = $kill['Victim']['Name'] ?? 'Unknown';
        $location = $kill['Location'] ?? 'Unknown';
        $killId = $kill['EventId'];
        $time = substr($kill['TimeStamp'], 0, 19) . 'Z';

        Http::post($this->killsWebhookUrl, [
            'embeds' => [[
                'title' => '⚔️ New Kill Alert!',
                'color' => 0xFF4444,
                'url' => "https://albiononline.com/killboard/kill/{$killId}",
                'timestamp' => $time,
                'fields' => [
                    ['name' => '🗡️ Killer', 'value' => $killer, 'inline' => true],
                    ['name' => '💀 Victim', 'value' => $victim, 'inline' => true],
                    ['name' => '📍 Location', 'value' => $location, 'inline' => false],
                ],
                'footer' => ['text' => "Kill ID: {$killId}"],
            ]]
        ]);
    }

    public function sendDeathAlert(array $death): void
    {
        $killer = $death['Killer']['Name'] ?? 'Unknown';
        $victim = $death['Victim']['Name'] ?? 'Unknown';
        $location = $death['Location'] ?? 'Unknown';
        $deathId = $death['EventId'];
        $time = substr($death['TimeStamp'], 0, 19) . 'Z';

        Http::post($this->deathsWebhookUrl, [
            'embeds' => [[
                'title' => '💀 Player Death Alert!',
                'color' => 0x808080,
                'url' => "https://albiononline.com/killboard/kill/{$deathId}",
                'timestamp' => $time,
                'fields' => [
                    ['name' => '💀 Victim', 'value' => $victim, 'inline' => true],
                    ['name' => '🗡️ Killer', 'value' => $killer, 'inline' => true],
                    ['name' => '📍 Location', 'value' => $location, 'inline' => false],
                ],
                'footer' => ['text' => "Death ID: {$deathId}"],
            ]]
        ]);
    }

    public function sendInventoryImage(string $base64Image, string $type = 'kill'): void
    {
        $webhookUrl = $type === 'kill' ? $this->killsWebhookUrl : $this->deathsWebhookUrl;

        Http::attach(
            'files[0]',
            base64_decode($base64Image),
            'inventory.png'
        )->post($webhookUrl, [
            'payload_json' => json_encode([
                'embeds' => [[
                    'title' => '🎒 Victim\'s Inventory',
                    'color' => 0x5865F2,
                    'image' => ['url' => 'attachment://inventory.png'],
                ]]
            ])
        ]);
    }
}
