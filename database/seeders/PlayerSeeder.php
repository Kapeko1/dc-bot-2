<?php

namespace Database\Seeders;

use App\Models\Player;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $players = [
            ['albion_id' => 'FVU3-y24QVutgVITx43C3w', 'name' => 'Kaps', 'active' => true],
            ['albion_id' => 'VP17RlqcSjqyav7DeRH9Kw', 'name' => 'Damis', 'active' => true],
            ['albion_id' => 'DOwYTo7aQSOiq-VUtJdMsQ', 'name' => 'Kinia', 'active' => true],
            ['albion_id' => 'vJRko2GFSBWOuQtxaZWLQA', 'name' => 'Mendus', 'active' => true],
            ['albion_id' => '_LINDX3aRQ-AwPistaggbw', 'name' => 'Lysy', 'active' => true],
        ];

        foreach ($players as $playerData) {
            Player::updateOrCreate(
                ['albion_id' => $playerData['albion_id']],
                $playerData
            );
        }

        $this->command->info('Added ' . count($players) . ' players.');
    }
}
