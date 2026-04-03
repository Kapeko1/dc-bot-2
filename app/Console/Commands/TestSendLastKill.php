<?php

namespace App\Console\Commands;

use App\Models\Player;
use App\Services\AlbionApiService;
use App\Services\DiscordWebhookService;
use App\Services\InventoryImageService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('albion:test-last-kill')]
#[Description('Test by sending the last kill of each active player to Discord')]
class TestSendLastKill extends Command
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
        $this->info('Sending last kill for each player...');

        $players = Player::where('active', true)->get();

        if ($players->isEmpty()) {
            $this->warn('No active players found.');
            return;
        }

        foreach ($players as $player) {
            $this->info("Processing {$player->name}...");

            $kills = $this->albion->getPlayerKills($player->albion_id);

            if (empty($kills)) {
                $this->warn("  - No kills found for {$player->name}");
                continue;
            }

            // Get the most recent kill (already sorted by API service)
            $lastKill = $kills[0];

            // Generate inventory image
            $inventoryImage = $this->getInventoryImage($lastKill);

            // Send to Discord
            $this->discord->sendKillAlert($lastKill, $inventoryImage);

            $this->info("  ✓ Sent last kill for {$player->name}");
        }

        $this->info('Done!');
    }

    private function getInventoryImage(array $event): ?string
    {
        $equipment = $event['Victim']['Equipment'] ?? [];
        $inventory = $event['Victim']['Inventory'] ?? [];
        $full = array_merge($equipment, array_filter($inventory));

        if (empty($full)) {
            return null;
        }

        return $this->imageService->generate($full);
    }
}
