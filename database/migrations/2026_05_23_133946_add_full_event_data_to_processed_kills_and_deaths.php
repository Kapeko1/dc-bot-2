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
            $table->json('event_data')->nullable();
            $table->string('killer_name')->nullable();
            $table->string('victim_name')->nullable();
            $table->string('killer_guild')->nullable();
            $table->string('victim_guild')->nullable();
            $table->integer('total_fame')->default(0);
            $table->string('killer_weapon')->nullable();
            $table->string('victim_weapon')->nullable();
            $table->timestamp('killed_at')->nullable();
            $table->integer('killer_ip')->default(0);
            $table->integer('victim_ip')->default(0);
            $table->integer('participants_count')->default(1);
        });

        Schema::table('processed_deaths', function (Blueprint $table) {
            $table->json('event_data')->nullable();
            $table->string('killer_name')->nullable();
            $table->string('victim_name')->nullable();
            $table->string('killer_guild')->nullable();
            $table->string('victim_guild')->nullable();
            $table->integer('total_fame')->default(0);
            $table->string('killer_weapon')->nullable();
            $table->string('victim_weapon')->nullable();
            $table->timestamp('killed_at')->nullable();
            $table->integer('killer_ip')->default(0);
            $table->integer('victim_ip')->default(0);
            $table->integer('participants_count')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('processed_kills', function (Blueprint $table) {
            $table->dropColumn([
                'event_data',
                'killer_name',
                'victim_name',
                'killer_guild',
                'victim_guild',
                'total_fame',
                'killer_weapon',
                'victim_weapon',
                'killed_at',
                'killer_ip',
                'victim_ip',
                'participants_count',
            ]);
        });

        Schema::table('processed_deaths', function (Blueprint $table) {
            $table->dropColumn([
                'event_data',
                'killer_name',
                'victim_name',
                'killer_guild',
                'victim_guild',
                'total_fame',
                'killer_weapon',
                'victim_weapon',
                'killed_at',
                'killer_ip',
                'victim_ip',
                'participants_count',
            ]);
        });
    }
};
