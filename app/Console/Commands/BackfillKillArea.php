<?php

namespace App\Console\Commands;

use App\Models\ProcessedDeath;
use App\Models\ProcessedKill;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('albion:backfill-kill-area')]
#[Description('Backfill kill_area field from event_data for existing records')]
class BackfillKillArea extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Starting kill_area backfill from existing event_data...');

        // Backfill kills
        $this->info('Processing kills...');
        $killsToUpdate = ProcessedKill::whereNull('kill_area')->orWhere('kill_area', '')->get();
        $killsUpdated = 0;

        foreach ($killsToUpdate as $kill) {
            $killArea = $kill->event_data['KillArea'] ?? 'UNKNOWN';
            $kill->update(['kill_area' => $killArea]);
            $killsUpdated++;
        }

        $this->info("Updated {$killsUpdated} kill(s)");

        // Backfill deaths
        $this->info('Processing deaths...');
        $deathsToUpdate = ProcessedDeath::whereNull('kill_area')->orWhere('kill_area', '')->get();
        $deathsUpdated = 0;

        foreach ($deathsToUpdate as $death) {
            $killArea = $death->event_data['KillArea'] ?? 'UNKNOWN';
            $death->update(['kill_area' => $killArea]);
            $deathsUpdated++;
        }

        $this->info("Updated {$deathsUpdated} death(s)");
        $this->info('Backfill complete!');
    }
}
