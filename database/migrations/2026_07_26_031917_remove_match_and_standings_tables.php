<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('match_games');
        Schema::dropIfExists('match_series');
        Schema::dropIfExists('tournament_stages');
        Schema::dropIfExists('tournament_standings');
    }

    public function down(): void
    {
        // Re-creation handled by original migrations if needed
    }
};
