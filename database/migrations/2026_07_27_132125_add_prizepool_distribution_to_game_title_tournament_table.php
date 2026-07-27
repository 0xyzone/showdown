<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('game_title_tournament', function (Blueprint $table) {
            $table->decimal('prize_pool', 12, 2)->default(0)->after('game_title_id');
            $table->text('prize_distribution')->nullable()->after('prize_pool');
        });
    }

    public function down(): void
    {
        Schema::table('game_title_tournament', function (Blueprint $table) {
            $table->dropColumn(['prize_pool', 'prize_distribution']);
        });
    }
};
