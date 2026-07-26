<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            if (! Schema::hasColumn('teams', 'game_title_id')) {
                $table->foreignId('game_title_id')->nullable()->after('manager_id')->constrained('game_titles')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            if (Schema::hasColumn('teams', 'game_title_id')) {
                $table->dropForeign(['game_title_id']);
                $table->dropColumn('game_title_id');
            }
        });
    }
};
