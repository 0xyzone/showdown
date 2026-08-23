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
        Schema::table('users', function (Blueprint $table) {
            $table->text('google_calendar_token')->nullable()->after('custom_fields');
            $table->timestamp('google_calendar_connected_at')->nullable()->after('google_calendar_token');
        });

        Schema::create('lead_meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->dateTime('meeting_start');
            $table->dateTime('meeting_end');
            $table->string('meeting_location_type')->default('online_meet'); // online_meet, in_person, phone
            $table->string('meeting_link')->nullable();
            $table->string('google_event_id')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('scheduled'); // scheduled, completed, cancelled, rescheduled
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_meetings');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['google_calendar_token', 'google_calendar_connected_at']);
        });
    }
};
