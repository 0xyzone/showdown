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
        Schema::table('game_title_tournament', function (Blueprint $table) {
            if (Schema::hasColumn('game_title_tournament', 'challonge_url')) {
                $table->dropColumn('challonge_url');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('game_title_tournament', function (Blueprint $table) {
            if (! Schema::hasColumn('game_title_tournament', 'challonge_url')) {
                $table->text('challonge_url')->nullable()->after('prize_distribution');
            }
        });
    }
};
