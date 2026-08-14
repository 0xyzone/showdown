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
        // 1. Add ticket_price to tournaments table if not present
        if (! Schema::hasColumn('tournaments', 'ticket_price')) {
            Schema::table('tournaments', function (Blueprint $table) {
                $table->decimal('ticket_price', 10, 2)->default(0.00)->after('prize_pool_total');
            });
        }

        // 2. Configurable Payment Methods table
        if (! Schema::hasTable('payment_methods')) {
            Schema::create('payment_methods', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->unique();
                $table->string('account_details')->nullable();
                $table->boolean('is_active')->default(true);
                $table->integer('order')->default(0);
                $table->timestamps();
            });
        }

        // 3. Ticket Purchases (Orders) table
        if (! Schema::hasTable('ticket_purchases')) {
            Schema::create('ticket_purchases', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tournament_id')->constrained()->cascadeOnDelete();
                $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
                $table->foreignId('payment_method_id')->nullable()->constrained('payment_methods')->nullOnDelete();
                $table->string('order_number')->unique();
                $table->string('customer_name');
                $table->string('customer_phone');
                $table->integer('quantity');
                $table->decimal('unit_price', 10, 2)->default(0.00);
                $table->decimal('total_amount', 10, 2)->default(0.00);
                $table->string('payment_status')->default('paid'); // unpaid, paid, cancelled
                $table->string('payment_source')->nullable(); // Snapshot of method name (e.g. Cash, Bank Transfer)
                $table->string('payment_reference')->nullable();
                $table->string('payment_receipt_path')->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 4. Individual Tickets table
        if (! Schema::hasTable('tickets')) {
            Schema::create('tickets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('ticket_purchase_id')->constrained()->cascadeOnDelete();
                $table->foreignId('tournament_id')->constrained()->cascadeOnDelete();
                $table->string('ticket_number')->unique();
                $table->uuid('verification_token')->unique();
                $table->string('customer_name');
                $table->string('customer_phone');
                $table->decimal('price', 10, 2)->default(0.00);
                $table->string('status')->default('valid'); // valid, used, cancelled
                $table->boolean('is_used')->default(false);
                $table->timestamp('used_at')->nullable();
                $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
                $table->string('verification_method')->nullable(); // qr_scan, manual_entry
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('ticket_purchases');
        Schema::dropIfExists('payment_methods');
        if (Schema::hasColumn('tournaments', 'ticket_price')) {
            Schema::table('tournaments', function (Blueprint $table) {
                $table->dropColumn('ticket_price');
            });
        }
    }
};
