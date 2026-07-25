<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tournaments', function (Blueprint $table) {
            $table->string('challonge_url')->nullable()->after('rules_doc_link');
            $table->string('challonge_embed_url')->nullable()->after('challonge_url');
            $table->string('discord_webhook_url')->nullable()->after('discord_server_url');
        });
    }

    public function down(): void
    {
        Schema::table('tournaments', function (Blueprint $table) {
            $table->dropColumn(['challonge_url', 'challonge_embed_url', 'discord_webhook_url']);
        });
    }
};
