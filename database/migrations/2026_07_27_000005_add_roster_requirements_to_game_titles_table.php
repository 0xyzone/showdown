<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('game_titles', function (Blueprint $table) {
            if (! Schema::hasColumn('game_titles', 'min_main_players')) {
                $table->integer('min_main_players')->default(5)->after('game_type');
                $table->integer('max_substitutes')->default(2)->after('min_main_players');
            }
        });
    }

    public function down(): void
    {
        Schema::table('game_titles', function (Blueprint $table) {
            $table->dropColumn(['min_main_players', 'max_substitutes']);
        });
    }
};
