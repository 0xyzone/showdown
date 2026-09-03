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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('campaign_code')->unique();
            $table->text('objectives')->nullable();
            $table->text('target_audience')->nullable();
            $table->decimal('budget', 14, 2)->default(0);
            $table->decimal('actual_spend', 14, 2)->default(0);
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status')->default('draft')->index();
            $table->string('priority')->default('medium')->index();
            $table->json('platforms')->nullable();
            $table->json('tags')->nullable();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('campaign_deliverables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('campaigns')->cascadeOnDelete();
            $table->string('title');
            $table->string('creative_type')->default('reels')->index();
            $table->string('platform')->nullable()->index();
            $table->text('copy_text')->nullable();
            $table->text('designer_notes')->nullable();
            $table->dateTime('scheduled_at')->nullable()->index();
            $table->string('approval_status')->default('pending_review')->index();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->json('asset_files')->nullable();
            $table->unsignedBigInteger('impressions')->default(0);
            $table->unsignedBigInteger('reach')->default(0);
            $table->unsignedBigInteger('conversions')->default(0);
            $table->unsignedBigInteger('clicks')->default(0);
            $table->decimal('spend', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('campaign_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('campaigns')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('role')->default('member');
            $table->timestamps();

            $table->unique(['campaign_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_user');
        Schema::dropIfExists('campaign_deliverables');
        Schema::dropIfExists('campaigns');
    }
};
