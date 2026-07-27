<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('game_title_tournament', function (Blueprint $table) {
            $table->text('challonge_url')->nullable()->after('prize_distribution');
        });
    }

    public function down(): void
    {
        Schema::table('game_title_tournament', function (Blueprint $table) {
            $table->dropColumn('challonge_url');
        });
    }
};
