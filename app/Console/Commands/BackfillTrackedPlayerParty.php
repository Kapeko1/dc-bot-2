<?php

namespace App\Console\Commands;

use App\Models\ProcessedDeath;
use App\Models\ProcessedKill;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('albion:backfill-tracked-player-party')]
#[Description('Backfill has_tracked_player_party field for existing kills and deaths')]
class BackfillTrackedPlayerParty extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Backfilling tracked player party status...');

        // Backfill kills
        $this->info('Processing kills...');
        $kills = ProcessedKill::whereNotNull('event_data')->get();
        $updatedKills = 0;

        foreach ($kills as $kill) {
            // For kills: tracked player is the killer
            // GroupMembers includes the killer, so party means GroupMembers > 1
            $hasTrackedPlayerParty = isset($kill->event_data['GroupMembers'])
                && count($kill->event_data['GroupMembers']) > 1;

            $kill->update(['has_tracked_player_party' => $hasTrackedPlayerParty]);
            $updatedKills++;
        }

        $this->info("Updated {$updatedKills} kills");

        // Backfill deaths
        $this->info('Processing deaths...');
        $deaths = ProcessedDeath::whereNotNull('event_data')->get();
        $updatedDeaths = 0;

        foreach ($deaths as $death) {
            // For deaths: tracked player is the victim
            // API doesn't provide victim's party info, set to false
            $death->update(['has_tracked_player_party' => false]);
            $updatedDeaths++;
        }

        $this->info("Updated {$updatedDeaths} deaths");
        $this->info('Backfill complete!');
    }
}
