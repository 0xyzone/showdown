<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sponsors', function (Blueprint $table) {
            $table->foreignId('tournament_id')->nullable()->after('id')->constrained('tournaments')->nullOnDelete();
        });

        Schema::table('partners', function (Blueprint $table) {
            $table->foreignId('tournament_id')->nullable()->after('id')->constrained('tournaments')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sponsors', function (Blueprint $table) {
            $table->dropForeign(['tournament_id']);
            $table->dropColumn('tournament_id');
        });

        Schema::table('partners', function (Blueprint $table) {
            $table->dropForeign(['tournament_id']);
            $table->dropColumn('tournament_id');
        });
    }
};
