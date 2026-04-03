<?php

namespace App\Console\Commands;

use App\Models\Player;
use App\Models\ProcessedDeath;
use App\Models\ProcessedKill;
use App\Services\AlbionApiService;
use App\Services\DiscordWebhookService;
use App\Services\InventoryImageService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('albion:track-kills')]
#[Description('Check for new kills and deaths for tracked players')]
class TrackKills extends Command
{
    public function __construct(
        private AlbionApiService $albion,
        private DiscordWebhookService $discord,
        private InventoryImageService $imageService,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Checking for new kills and deaths...');

        $players = Player::where('active', true)->get();

        if ($players->isEmpty()) {
            $this->warn('No active players to track.');
            return;
        }

        foreach ($players as $player) {
            $this->info("Checking player: {$player->name} ({$player->albion_id})");
            $this->checkPlayer($player);
        }

        $this->info('Done!');
    }

    private function checkPlayer(Player $player): void
    {
        $this->checkKills($player);
        $this->checkDeaths($player);
    }

    private function checkKills(Player $player): void
    {
        $kills = $this->albion->getPlayerKills($player->albion_id);

        $newKills = 0;
        foreach ($kills as $kill) {
            $eventId = $kill['EventId'];

            if (ProcessedKill::where('event_id', $eventId)->exists()) {
                continue;
            }

            $this->discord->sendKillAlert($kill);
            $this->sendInventoryIfPresent($kill, 'kill');

            ProcessedKill::create([
                'event_id' => $eventId,
                'albion_player_id' => $player->albion_id,
            ]);

            $newKills++;
        }

        if ($newKills > 0) {
            $this->info("  - Found {$newKills} new kill(s)");
        }
    }

    private function checkDeaths(Player $player): void
    {
        $deaths = $this->albion->getPlayerDeaths($player->albion_id);

        $newDeaths = 0;
        foreach ($deaths as $death) {
            $eventId = $death['EventId'];

            if (ProcessedDeath::where('event_id', $eventId)->exists()) {
                continue;
            }

            $this->discord->sendDeathAlert($death);
            $this->sendInventoryIfPresent($death, 'death');

            ProcessedDeath::create([
                'event_id' => $eventId,
                'albion_player_id' => $player->albion_id,
            ]);

            $newDeaths++;
        }

        if ($newDeaths > 0) {
            $this->info("  - Found {$newDeaths} new death(s)");
        }
    }

    private function sendInventoryIfPresent(array $event, string $type): void
    {
        $equipment = $event['Victim']['Equipment'] ?? [];
        $inventory = $event['Victim']['Inventory'] ?? [];
        $full = array_merge($equipment, array_filter($inventory));

        if (!empty($full)) {
            $image = $this->imageService->generate($full);
            if ($image) {
                $this->discord->sendInventoryImage($image, $type);
            }
        }
    }
}
