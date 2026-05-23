<?php

namespace App\Console\Commands;

use App\Models\ProcessedDeath;
use App\Models\ProcessedKill;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

#[Signature('albion:update-existing-records')]
#[Description('Update existing kill/death records with full event data from Albion API')]
class UpdateExistingRecords extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Updating existing records with full event data...');

        $this->updateKills();
        $this->updateDeaths();

        $this->newLine();
        $this->info('Update complete!');
    }

    private function updateKills(): void
    {
        $kills = ProcessedKill::whereNull('event_data')->orWhereNull('killer_name')->get();

        $this->info("Updating {$kills->count()} kill records...");
        $bar = $this->output->createProgressBar($kills->count());

        foreach ($kills as $kill) {
            $eventData = $this->fetchEventData($kill->event_id);

            if ($eventData) {
                $kill->update([
                    'event_data' => $eventData,
                    'killer_name' => $eventData['Killer']['Name'] ?? null,
                    'victim_name' => $eventData['Victim']['Name'] ?? null,
                    'killer_guild' => $eventData['Killer']['GuildName'] ?? null,
                    'victim_guild' => $eventData['Victim']['GuildName'] ?? null,
                    'total_fame' => $eventData['TotalVictimKillFame'] ?? 0,
                    'killer_weapon' => $eventData['Killer']['Equipment']['MainHand']['Type'] ?? null,
                    'victim_weapon' => $eventData['Victim']['Equipment']['MainHand']['Type'] ?? null,
                    'killed_at' => isset($eventData['TimeStamp']) ? date('Y-m-d H:i:s', strtotime($eventData['TimeStamp'])) : null,
                    'killer_ip' => $eventData['Killer']['AverageItemPower'] ?? 0,
                    'victim_ip' => $eventData['Victim']['AverageItemPower'] ?? 0,
                    'participants_count' => count($eventData['Participants'] ?? []) + 1,
                ]);
            }

            $bar->advance();
            usleep(100000); // 100ms delay to avoid rate limiting
        }

        $bar->finish();
        $this->newLine();
    }

    private function updateDeaths(): void
    {
        $deaths = ProcessedDeath::whereNull('event_data')->orWhereNull('killer_name')->get();

        $this->info("Updating {$deaths->count()} death records...");
        $bar = $this->output->createProgressBar($deaths->count());

        foreach ($deaths as $death) {
            $eventData = $this->fetchEventData($death->event_id);

            if ($eventData) {
                $death->update([
                    'event_data' => $eventData,
                    'killer_name' => $eventData['Killer']['Name'] ?? null,
                    'victim_name' => $eventData['Victim']['Name'] ?? null,
                    'killer_guild' => $eventData['Killer']['GuildName'] ?? null,
                    'victim_guild' => $eventData['Victim']['GuildName'] ?? null,
                    'total_fame' => $eventData['TotalVictimKillFame'] ?? 0,
                    'killer_weapon' => $eventData['Killer']['Equipment']['MainHand']['Type'] ?? null,
                    'victim_weapon' => $eventData['Victim']['Equipment']['MainHand']['Type'] ?? null,
                    'killed_at' => isset($eventData['TimeStamp']) ? date('Y-m-d H:i:s', strtotime($eventData['TimeStamp'])) : null,
                    'killer_ip' => $eventData['Killer']['AverageItemPower'] ?? 0,
                    'victim_ip' => $eventData['Victim']['AverageItemPower'] ?? 0,
                    'participants_count' => count($eventData['Participants'] ?? []) + 1,
                ]);
            }

            $bar->advance();
            usleep(100000); // 100ms delay to avoid rate limiting
        }

        $bar->finish();
        $this->newLine();
    }

    private function fetchEventData(string $eventId): ?array
    {
        try {
            $response = Http::timeout(5)->get("https://gameinfo-ams.albiononline.com/api/gameinfo/events/{$eventId}");

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            $this->error("Failed to fetch event {$eventId}: {$e->getMessage()}");
        }

        return null;
    }
}
