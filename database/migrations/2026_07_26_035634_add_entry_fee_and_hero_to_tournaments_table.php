<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tournaments', function (Blueprint $table) {
            $table->decimal('entry_fee', 10, 2)->default(100.00)->after('prize_pool_total');
            $table->string('hero_headline')->nullable()->after('description');
            $table->text('hero_subheadline')->nullable()->after('hero_headline');
        });
    }

    public function down(): void
    {
        Schema::table('tournaments', function (Blueprint $table) {
            $table->dropColumn(['entry_fee', 'hero_headline', 'hero_subheadline']);
        });
    }
};
