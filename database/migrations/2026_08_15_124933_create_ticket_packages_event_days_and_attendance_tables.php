<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tournament Event Days
        if (! Schema::hasTable('tournament_event_days')) {
            Schema::create('tournament_event_days', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tournament_id')->constrained()->cascadeOnDelete();
                $table->string('day_name'); // e.g. "Day 1 - Group Stage"
                $table->date('event_date');
                $table->integer('order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 2. Ticket Packages
        if (! Schema::hasTable('ticket_packages')) {
            Schema::create('ticket_packages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tournament_id')->constrained()->cascadeOnDelete();
                $table->string('name');
                $table->text('description')->nullable();
                $table->decimal('price', 10, 2)->default(0.00);
                $table->string('validity_type')->default('all_days'); // all_days, specific_days, single_day
                $table->boolean('is_active')->default(true);
                $table->integer('order')->default(0);
                $table->timestamps();
            });
        }

        // 3. Ticket Package Default Event Days (pivot)
        if (! Schema::hasTable('ticket_package_event_day')) {
            Schema::create('ticket_package_event_day', function (Blueprint $table) {
                $table->id();
                $table->foreignId('ticket_package_id')->constrained()->cascadeOnDelete();
                $table->foreignId('tournament_event_day_id')->constrained()->cascadeOnDelete();
                $table->timestamps();
            });
        }

        // 4. Ticket Specific Event Day Validity (pivot)
        if (! Schema::hasTable('ticket_event_day')) {
            Schema::create('ticket_event_day', function (Blueprint $table) {
                $table->id();
                $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();
                $table->foreignId('tournament_event_day_id')->constrained()->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['ticket_id', 'tournament_event_day_id'], 'ticket_event_day_unique');
            });
        }

        // 5. Ticket Attendance (Per-Day Check-in History)
        if (! Schema::hasTable('ticket_attendances')) {
            Schema::create('ticket_attendances', function (Blueprint $table) {
                $table->id();
                $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();
                $table->foreignId('tournament_event_day_id')->constrained()->cascadeOnDelete();
                $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('verified_at');
                $table->string('verification_method')->default('qr_scan'); // qr_scan, manual_entry
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->unique(['ticket_id', 'tournament_event_day_id'], 'ticket_day_attendance_unique');
            });
        }

        // 6. Update ticket_purchases table
        Schema::table('ticket_purchases', function (Blueprint $table) {
            if (! Schema::hasColumn('ticket_purchases', 'ticket_package_id')) {
                $table->foreignId('ticket_package_id')->nullable()->after('tournament_id')->constrained('ticket_packages')->nullOnDelete();
            }
            if (! Schema::hasColumn('ticket_purchases', 'package_name')) {
                $table->string('package_name')->nullable()->after('ticket_package_id');
            }
            if (! Schema::hasColumn('ticket_purchases', 'seller_id')) {
                $table->foreignId('seller_id')->nullable()->after('created_by')->constrained('users')->cascadeOnDelete();
            }
        });

        // 7. Update tickets table
        Schema::table('tickets', function (Blueprint $table) {
            if (! Schema::hasColumn('tickets', 'ticket_package_id')) {
                $table->foreignId('ticket_package_id')->nullable()->after('tournament_id')->constrained('ticket_packages')->nullOnDelete();
            }
            if (! Schema::hasColumn('tickets', 'package_name')) {
                $table->string('package_name')->nullable()->after('ticket_package_id');
            }
        });

        // Backfill seller_id from created_by for existing ticket_purchases
        DB::statement('UPDATE ticket_purchases SET seller_id = created_by WHERE seller_id IS NULL AND created_by IS NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (Schema::hasColumn('tickets', 'ticket_package_id')) {
                $table->dropForeign(['ticket_package_id']);
                $table->dropColumn('ticket_package_id');
            }
            if (Schema::hasColumn('tickets', 'package_name')) {
                $table->dropColumn('package_name');
            }
        });

        Schema::table('ticket_purchases', function (Blueprint $table) {
            if (Schema::hasColumn('ticket_purchases', 'ticket_package_id')) {
                $table->dropForeign(['ticket_package_id']);
                $table->dropColumn('ticket_package_id');
            }
            if (Schema::hasColumn('ticket_purchases', 'package_name')) {
                $table->dropColumn('package_name');
            }
            if (Schema::hasColumn('ticket_purchases', 'seller_id')) {
                $table->dropForeign(['seller_id']);
                $table->dropColumn('seller_id');
            }
        });

        Schema::dropIfExists('ticket_attendances');
        Schema::dropIfExists('ticket_event_day');
        Schema::dropIfExists('ticket_package_event_day');
        Schema::dropIfExists('ticket_packages');
        Schema::dropIfExists('tournament_event_days');
    }
};
