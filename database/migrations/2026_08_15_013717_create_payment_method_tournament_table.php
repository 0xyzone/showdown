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
        if (! Schema::hasTable('payment_method_tournament')) {
            Schema::create('payment_method_tournament', function (Blueprint $table) {
                $table->id();
                $table->foreignId('payment_method_id')->constrained()->cascadeOnDelete();
                $table->foreignId('tournament_id')->constrained()->cascadeOnDelete();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_method_tournament');
    }
};
