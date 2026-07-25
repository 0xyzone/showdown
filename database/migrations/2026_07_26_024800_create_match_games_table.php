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
        Schema::create('match_games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_series_id')->constrained('match_series')->cascadeOnDelete();
            $table->integer('game_number')->default(1);
            $table->integer('team_a_score')->default(0);
            $table->integer('team_b_score')->default(0);
            $table->foreignId('winner_team_id')->nullable()->constrained('teams')->nullOnDelete();
            $table->integer('match_duration_seconds')->nullable();
            $table->json('stats_payload')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_games');
    }
};
