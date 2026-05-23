<?php

namespace App\Console\Commands;

use App\Models\Player;
use App\Models\ProcessedDeath;
use App\Models\ProcessedKill;
use App\Services\AlbionApiService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('albion:backfill-history')]
#[Description('Backfill historical kill and death data for all tracked players')]
class BackfillKillHistory extends Command
{
    public function __construct(
        private AlbionApiService $albion,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Starting historical data backfill...');

        $players = Player::where('active', true)->get();

        if ($players->isEmpty()) {
            $this->warn('No active players to track.');
            return;
        }

        $this->info("Found {$players->count()} active player(s) to backfill");

        foreach ($players as $player) {
            $this->info("Processing player: {$player->name} ({$player->albion_id})");
            $this->backfillPlayer($player);
        }

        $this->newLine();
        $this->info('Backfill complete!');

        $totalKills = ProcessedKill::count();
        $totalDeaths = ProcessedDeath::count();
        $this->info("Total records in database: {$totalKills} kills, {$totalDeaths} deaths");
    }

    private function backfillPlayer(Player $player): void
    {
        $this->backfillKills($player);
        $this->backfillDeaths($player);
    }

    private function backfillKills(Player $player): void
    {
        $kills = $this->albion->getPlayerKills($player->albion_id);

        $newKills = 0;
        $skipped = 0;

        foreach ($kills as $kill) {
            $eventId = $kill['EventId'];

            if (ProcessedKill::where('event_id', $eventId)->exists()) {
                $skipped++;
                continue;
            }

            ProcessedKill::create([
                'event_id' => $eventId,
                'albion_player_id' => $player->albion_id,
                'event_data' => $kill,
                'killer_name' => $kill['Killer']['Name'] ?? null,
                'victim_name' => $kill['Victim']['Name'] ?? null,
                'killer_guild' => $kill['Killer']['GuildName'] ?? null,
                'victim_guild' => $kill['Victim']['GuildName'] ?? null,
                'total_fame' => $kill['TotalVictimKillFame'] ?? 0,
                'killer_weapon' => $kill['Killer']['Equipment']['MainHand']['Type'] ?? null,
                'victim_weapon' => $kill['Victim']['Equipment']['MainHand']['Type'] ?? null,
                'killed_at' => isset($kill['TimeStamp']) ? date('Y-m-d H:i:s', strtotime($kill['TimeStamp'])) : null,
                'killer_ip' => $kill['Killer']['AverageItemPower'] ?? 0,
                'victim_ip' => $kill['Victim']['AverageItemPower'] ?? 0,
                'participants_count' => count($kill['Participants'] ?? []) + 1,
            ]);

            $newKills++;
        }

        $this->line("  Kills: +{$newKills} new, {$skipped} already exist");
    }

    private function backfillDeaths(Player $player): void
    {
        $deaths = $this->albion->getPlayerDeaths($player->albion_id);

        $newDeaths = 0;
        $skipped = 0;

        foreach ($deaths as $death) {
            $eventId = $death['EventId'];

            if (ProcessedDeath::where('event_id', $eventId)->exists()) {
                $skipped++;
                continue;
            }

            ProcessedDeath::create([
                'event_id' => $eventId,
                'albion_player_id' => $player->albion_id,
                'event_data' => $death,
                'killer_name' => $death['Killer']['Name'] ?? null,
                'victim_name' => $death['Victim']['Name'] ?? null,
                'killer_guild' => $death['Killer']['GuildName'] ?? null,
                'victim_guild' => $death['Victim']['GuildName'] ?? null,
                'total_fame' => $death['TotalVictimKillFame'] ?? 0,
                'killer_weapon' => $death['Killer']['Equipment']['MainHand']['Type'] ?? null,
                'victim_weapon' => $death['Victim']['Equipment']['MainHand']['Type'] ?? null,
                'killed_at' => isset($death['TimeStamp']) ? date('Y-m-d H:i:s', strtotime($death['TimeStamp'])) : null,
                'killer_ip' => $death['Killer']['AverageItemPower'] ?? 0,
                'victim_ip' => $death['Victim']['AverageItemPower'] ?? 0,
                'participants_count' => count($death['Participants'] ?? []) + 1,
            ]);

            $newDeaths++;
        }

        $this->line("  Deaths: +{$newDeaths} new, {$skipped} already exist");
    }
}
