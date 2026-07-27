<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tournaments', function (Blueprint $table) {
            if (Schema::hasColumn('tournaments', 'challonge_url')) {
                $table->dropColumn('challonge_url');
            }
            if (Schema::hasColumn('tournaments', 'challonge_embed_url')) {
                $table->dropColumn('challonge_embed_url');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tournaments', function (Blueprint $table) {
            $table->string('challonge_url')->nullable();
            $table->string('challonge_embed_url')->nullable();
        });
    }
};
