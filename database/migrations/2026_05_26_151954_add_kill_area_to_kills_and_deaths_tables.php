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
            $table->string('kill_area')->nullable()->after('participants_count');
        });

        Schema::table('processed_deaths', function (Blueprint $table) {
            $table->string('kill_area')->nullable()->after('participants_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('processed_kills', function (Blueprint $table) {
            $table->dropColumn('kill_area');
        });

        Schema::table('processed_deaths', function (Blueprint $table) {
            $table->dropColumn('kill_area');
        });
    }
};
