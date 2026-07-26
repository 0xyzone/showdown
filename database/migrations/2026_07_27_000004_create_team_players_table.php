<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->string('full_name');
            $table->string('role')->default('main_player'); // main_player, substitute, coach, manager
            $table->date('date_of_birth')->nullable();
            $table->string('front_photo_path')->nullable(); // Front facing hands folded photo
            $table->string('ign')->nullable(); // In-game ID/Name
            $table->string('ingame_role')->nullable(); // e.g. Jungler, Entry Fragger, Sniper, Support
            $table->string('whatsapp_number')->nullable();
            $table->string('email')->nullable();
            $table->string('discord_id')->nullable();
            $table->string('citizenship_number')->nullable();
            $table->string('citizenship_front_path')->nullable();
            $table->string('citizenship_back_path')->nullable();
            $table->string('ingame_profile_screenshot_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_players');
    }
};
