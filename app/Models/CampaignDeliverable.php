<?php

namespace App\Models;

use App\Enums\DeliverableApprovalStatus;
use App\Enums\DeliverableType;
use App\Enums\MarketingPlatform;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignDeliverable extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'title',
        'creative_type',
        'platform',
        'copy_text',
        'designer_notes',
        'scheduled_at',
        'approval_status',
        'assigned_to',
        'asset_files',
        'impressions',
        'reach',
        'conversions',
        'clicks',
        'spend',
    ];

    protected $casts = [
        'creative_type' => DeliverableType::class,
        'platform' => MarketingPlatform::class,
        'approval_status' => DeliverableApprovalStatus::class,
        'scheduled_at' => 'datetime',
        'asset_files' => 'array',
        'impressions' => 'integer',
        'reach' => 'integer',
        'conversions' => 'integer',
        'clicks' => 'integer',
        'spend' => 'decimal:2',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
