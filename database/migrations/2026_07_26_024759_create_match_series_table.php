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
        Schema::create('match_series', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_stage_id')->constrained('tournament_stages')->cascadeOnDelete();
            $table->foreignId('team_a_id')->nullable()->constrained('teams')->nullOnDelete();
            $table->foreignId('team_b_id')->nullable()->constrained('teams')->nullOnDelete();
            $table->integer('best_of')->default(3);
            $table->dateTime('scheduled_at')->nullable();
            $table->string('status')->default('scheduled'); // scheduled, live, completed, postponed
            $table->foreignId('winner_team_id')->nullable()->constrained('teams')->nullOnDelete();
            $table->string('stream_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_series');
    }
};
