<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. System-wide Attendance Settings
        Schema::create('attendance_settings', function (Blueprint $table) {
            $table->id();
            $table->string('office_name')->default('Showdown HQ');
            $table->decimal('office_latitude', 10, 7)->default(27.7172453); // Default Kathmandu / configurable
            $table->decimal('office_longitude', 10, 7)->default(85.3239605);
            $table->unsignedInteger('allowed_radius_meters')->default(150);
            $table->unsignedInteger('max_gps_accuracy_meters')->default(100);
            $table->boolean('is_network_restriction_enabled')->default(false);
            $table->json('allowed_ip_addresses')->nullable();
            $table->boolean('require_biometric')->default(true);
            $table->unsignedInteger('max_devices_per_user')->default(3);
            $table->timestamps();
        });

        // 2. Staff Attendance Profiles (Policy per staff member)
        Schema::create('staff_attendance_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->unique();
            $table->string('attendance_mode')->default('office_only'); // office_only, remote_allowed, office_and_network, flexible
            $table->boolean('is_biometric_exempt')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 3. Staff Biometric Credentials (W3C WebAuthn Passkeys)
        Schema::create('staff_biometric_credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name')->default('Primary Device');
            $table->string('credential_id', 512)->unique(); // Base64url encoded binary ID
            $table->text('public_key'); // PEM encoded or COSE public key
            $table->unsignedBigInteger('counter')->default(0);
            $table->string('aaguid', 64)->nullable();
            $table->string('attestation_type', 64)->nullable();
            $table->json('transports')->nullable();
            $table->string('device_type', 128)->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['user_id', 'is_active']);
        });

        // 4. Staff Daily Attendances & Timesheets
        Schema::create('staff_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date')->index();
            $table->dateTime('punch_in_at')->nullable();
            $table->dateTime('punch_out_at')->nullable();
            $table->unsignedInteger('worked_minutes')->default(0);
            $table->string('status')->default('working'); // working, completed, remote, half_day, manually_corrected
            $table->string('location_mode')->default('office'); // office, remote, manual_correction

            // Punch-in verification audit
            $table->decimal('punch_in_latitude', 10, 7)->nullable();
            $table->decimal('punch_in_longitude', 10, 7)->nullable();
            $table->decimal('punch_in_accuracy', 8, 2)->nullable();
            $table->unsignedInteger('punch_in_distance_meters')->nullable();
            $table->string('punch_in_ip', 45)->nullable();
            $table->string('punch_in_method')->default('webauthn'); // webauthn, manual_override
            $table->boolean('punch_in_verified_biometric')->default(false);

            // Punch-out verification audit
            $table->decimal('punch_out_latitude', 10, 7)->nullable();
            $table->decimal('punch_out_longitude', 10, 7)->nullable();
            $table->decimal('punch_out_accuracy', 8, 2)->nullable();
            $table->unsignedInteger('punch_out_distance_meters')->nullable();
            $table->string('punch_out_ip', 45)->nullable();
            $table->string('punch_out_method')->nullable();
            $table->boolean('punch_out_verified_biometric')->default(false);

            // Manual correction audit
            $table->boolean('is_manually_corrected')->default(false);
            $table->text('correction_reason')->nullable();
            $table->foreignId('corrected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('corrected_at')->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'date']);
        });

        // 5. Staff Punch Events (Immutable Security Audit Log)
        Schema::create('staff_punch_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('staff_attendance_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event_type'); // punch_in, punch_out, credential_registered, credential_revoked, manual_correction
            $table->dateTime('occurred_at');
            $table->string('status')->default('success'); // success, rejected
            $table->string('failure_reason')->nullable();
            $table->string('location_mode')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('accuracy', 8, 2)->nullable();
            $table->unsignedInteger('distance_meters')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('verification_method')->nullable();
            $table->string('credential_id', 512)->nullable();
            $table->json('details')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'event_type']);
            $table->index(['occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_punch_events');
        Schema::dropIfExists('staff_attendances');
        Schema::dropIfExists('staff_biometric_credentials');
        Schema::dropIfExists('staff_attendance_profiles');
        Schema::dropIfExists('attendance_settings');
    }
};
