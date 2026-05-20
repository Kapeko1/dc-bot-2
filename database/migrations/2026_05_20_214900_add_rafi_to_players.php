<?php

use App\Models\Player;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $players = [
            ['albion_id' => 'XpLQm1DWTYqhBiQCLerUqA', 'name' => 'LeMamul', 'active' => true],

        ];
        foreach ($players as $playerData) {
            Player::updateOrCreate(
                ['albion_id' => $playerData['albion_id']],
                $playerData
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('players', function (Blueprint $table) {
            //
        });
    }
};
