<?php

namespace App\Console\Commands;

use App\Models\Player;
use App\Models\ProcessedDeath;
use App\Models\ProcessedKill;
use App\Services\AlbionApiService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('albion:initialize-history')]
#[Description('Initialize player history without sending notifications')]
class InitializePlayerHistory extends Command
{
    public function __construct(private AlbionApiService $albion)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Initializing player history...');

        $players = Player::where('active', true)->get();

        if ($players->isEmpty()) {
            $this->warn('No active players to initialize.');
            return;
        }

        foreach ($players as $player) {
            $this->info("Processing {$player->name} ({$player->albion_id})...");
            $this->initializePlayer($player);
        }

        $this->info('Done!');
    }

    private function initializePlayer(Player $player): void
    {
        // Mark all existing kills as processed
        $kills = $this->albion->getPlayerKills($player->albion_id);
        $killCount = 0;

        foreach ($kills as $kill) {
            $eventId = $kill['EventId'];

            if (!ProcessedKill::where('event_id', $eventId)->exists()) {
                ProcessedKill::create([
                    'event_id' => $eventId,
                    'albion_player_id' => $player->albion_id,
                ]);
                $killCount++;
            }
        }

        // Mark all existing deaths as processed
        $deaths = $this->albion->getPlayerDeaths($player->albion_id);
        $deathCount = 0;

        foreach ($deaths as $death) {
            $eventId = $death['EventId'];

            if (!ProcessedDeath::where('event_id', $eventId)->exists()) {
                ProcessedDeath::create([
                    'event_id' => $eventId,
                    'albion_player_id' => $player->albion_id,
                ]);
                $deathCount++;
            }
        }

        $this->info("  - Marked {$killCount} kills and {$deathCount} deaths as processed");
    }
}
