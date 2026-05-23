<?php

namespace App\Console\Commands;

use App\Models\ItemIconCache;
use App\Models\ProcessedDeath;
use App\Models\ProcessedKill;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

#[Signature('albion:prewarm-icon-cache')]
#[Description('Pre-fetch and cache all item icons from kills/deaths')]
class PrewarmIconCache extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Collecting unique items from kills and deaths...');

        // Collect all unique weapons
        $weapons = collect()
            ->merge(ProcessedKill::whereNotNull('killer_weapon')->pluck('killer_weapon'))
            ->merge(ProcessedKill::whereNotNull('victim_weapon')->pluck('victim_weapon'))
            ->merge(ProcessedDeath::whereNotNull('killer_weapon')->pluck('killer_weapon'))
            ->merge(ProcessedDeath::whereNotNull('victim_weapon')->pluck('victim_weapon'))
            ->unique()
            ->filter()
            ->values();

        // Collect all equipment items from event_data
        $equipmentItems = collect();

        ProcessedKill::whereNotNull('event_data')->chunk(100, function ($kills) use ($equipmentItems) {
            foreach ($kills as $kill) {
                $this->extractItems($kill->event_data, $equipmentItems);
            }
        });

        ProcessedDeath::whereNotNull('event_data')->chunk(100, function ($deaths) use ($equipmentItems) {
            foreach ($deaths as $death) {
                $this->extractItems($death->event_data, $equipmentItems);
            }
        });

        $allItems = $weapons->merge($equipmentItems)->unique()->filter()->values();

        $this->info("Found {$allItems->count()} unique items to cache");

        $bar = $this->output->createProgressBar($allItems->count());
        $cached = 0;
        $fetched = 0;
        $failed = 0;

        foreach ($allItems as $itemType) {
            // Check if already cached
            if (ItemIconCache::where('item_type', $itemType)->exists()) {
                $cached++;
                $bar->advance();
                continue;
            }

            // Fetch from API
            $result = $this->fetchAndCacheIcon($itemType);

            if ($result === true) {
                $fetched++;
            } else {
                $failed++;
            }

            $bar->advance();
            usleep(50000); // 50ms delay to avoid rate limiting
        }

        $bar->finish();
        $this->newLine();
        $this->newLine();
        $this->info("Cache warming complete!");
        $this->info("Already cached: {$cached}");
        $this->info("Newly fetched: {$fetched}");
        $this->info("Failed: {$failed}");
    }

    private function extractItems(array $eventData, $collection): void
    {
        // Extract killer equipment
        if (isset($eventData['Killer']['Equipment'])) {
            foreach ($eventData['Killer']['Equipment'] as $item) {
                if (isset($item['Type']) && !empty($item['Type'])) {
                    $collection->push($item['Type']);
                }
            }
        }

        // Extract killer inventory
        if (isset($eventData['Killer']['Inventory'])) {
            foreach ($eventData['Killer']['Inventory'] as $item) {
                if (isset($item['Type']) && !empty($item['Type'])) {
                    $collection->push($item['Type']);
                }
            }
        }

        // Extract victim equipment
        if (isset($eventData['Victim']['Equipment'])) {
            foreach ($eventData['Victim']['Equipment'] as $item) {
                if (isset($item['Type']) && !empty($item['Type'])) {
                    $collection->push($item['Type']);
                }
            }
        }

        // Extract victim inventory
        if (isset($eventData['Victim']['Inventory'])) {
            foreach ($eventData['Victim']['Inventory'] as $item) {
                if (isset($item['Type']) && !empty($item['Type'])) {
                    $collection->push($item['Type']);
                }
            }
        }
    }

    private function fetchAndCacheIcon(string $itemType): bool
    {
        try {
            $iconUrl = "https://render.albiononline.com/v1/item/{$itemType}.png?size=64";
            $response = Http::timeout(5)->retry(2, 100)->get($iconUrl);

            if ($response->successful()) {
                ItemIconCache::create([
                    'item_type' => $itemType,
                    'image_data' => base64_encode($response->body()),
                ]);
                return true;
            } else {
                // Cache the failure
                ItemIconCache::create([
                    'item_type' => $itemType,
                    'image_data' => 'FAILED',
                ]);
                return false;
            }
        } catch (\Exception $e) {
            // Cache the failure
            ItemIconCache::create([
                'item_type' => $itemType,
                'image_data' => 'FAILED',
            ]);
            return false;
        }
    }
}
