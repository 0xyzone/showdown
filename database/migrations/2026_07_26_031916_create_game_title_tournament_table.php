<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_title_tournament', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained('tournaments')->cascadeOnDelete();
            $table->foreignId('game_title_id')->constrained('game_titles')->cascadeOnDelete();
            $table->timestamps();
        });

        if (Schema::hasColumn('tournaments', 'game_title_id')) {
            Schema::table('tournaments', function (Blueprint $table) {
                $table->dropForeign(['game_title_id']);
                $table->dropColumn('game_title_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('game_title_tournament');
    }
};
