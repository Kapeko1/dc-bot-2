<?php

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
        Schema::table('processed_kills', function (Blueprint $table) {
            // For kills: tracked player is the killer, so check if killer had party
            $table->boolean('has_tracked_player_party')->default(false)->after('participants_count');
        });

        Schema::table('processed_deaths', function (Blueprint $table) {
            // For deaths: tracked player is the victim, so check if victim had party
            $table->boolean('has_tracked_player_party')->default(false)->after('participants_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('processed_kills', function (Blueprint $table) {
            $table->dropColumn('has_tracked_player_party');
        });

        Schema::table('processed_deaths', function (Blueprint $table) {
            $table->dropColumn('has_tracked_player_party');
        });
    }
};
