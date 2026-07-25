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
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_title_id')->constrained('game_titles')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('season_version')->default('2026 Vol-I');
            $table->string('logo_path')->nullable();
            $table->string('banner_path')->nullable();
            $table->text('description')->nullable();
            $table->string('rules_doc_link')->nullable();
            $table->string('discord_server_url')->nullable();
            $table->string('linktree_url')->nullable();
            $table->json('custom_links')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->dateTime('registration_start')->nullable();
            $table->dateTime('registration_end')->nullable();
            $table->string('status')->default('registration_open'); // draft, registration_open, ongoing, completed, cancelled
            $table->decimal('prize_pool_total', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournaments');
    }
};
