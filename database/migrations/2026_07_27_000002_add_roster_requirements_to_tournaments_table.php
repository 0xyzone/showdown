<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tournaments', function (Blueprint $table) {
            if (! Schema::hasColumn('tournaments', 'min_main_players')) {
                $table->integer('min_main_players')->default(5)->after('entry_fee');
                $table->integer('max_main_players')->default(5)->after('min_main_players');
                $table->integer('max_substitutes')->default(2)->after('max_main_players');
                $table->boolean('require_coach')->default(false)->after('max_substitutes');
                $table->boolean('require_manager')->default(false)->after('require_coach');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tournaments', function (Blueprint $table) {
            $table->dropColumn([
                'min_main_players',
                'max_main_players',
                'max_substitutes',
                'require_coach',
                'require_manager',
            ]);
        });
    }
};
