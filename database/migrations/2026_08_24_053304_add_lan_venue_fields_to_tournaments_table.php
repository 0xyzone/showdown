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
        Schema::table('tournaments', function (Blueprint $table) {
            $table->boolean('is_lan')->default(false)->after('theme_color');
            $table->string('venue_name')->nullable()->after('is_lan');
            $table->string('venue_address')->nullable()->after('venue_name');
            $table->string('venue_map_url')->nullable()->after('venue_address');
            $table->text('venue_notes')->nullable()->after('venue_map_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tournaments', function (Blueprint $table) {
            $table->dropColumn([
                'is_lan',
                'venue_name',
                'venue_address',
                'venue_map_url',
                'venue_notes',
            ]);
        });
    }
};
