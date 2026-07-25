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
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('title')->default('Official Partner'); // Media Partner, Hospitality Partner, Beverage Partner, etc.
            $table->string('logo_url')->nullable();
            $table->string('website_url')->nullable();
            $table->string('level')->default('standard'); // major, standard
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('sponsor_query_id')->nullable()->constrained('sponsor_queries')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partners');
    }
};
