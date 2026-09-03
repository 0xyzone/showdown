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
        Schema::table('campaign_deliverables', function (Blueprint $table) {
            $table->json('platforms')->nullable()->after('creative_type');
        });

        // Migrate existing singular platform column data into platforms array
        $deliverables = DB::table('campaign_deliverables')->whereNotNull('platform')->get(['id', 'platform']);
        foreach ($deliverables as $deliverable) {
            if (! empty($deliverable->platform)) {
                DB::table('campaign_deliverables')
                    ->where('id', $deliverable->id)
                    ->update(['platforms' => json_encode([$deliverable->platform])]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaign_deliverables', function (Blueprint $table) {
            $table->dropColumn('platforms');
        });
    }
};
