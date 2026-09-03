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
        'platforms',
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
        'platforms' => 'array',
        'approval_status' => DeliverableApprovalStatus::class,
        'scheduled_at' => 'datetime',
        'asset_files' => 'array',
        'impressions' => 'integer',
        'reach' => 'integer',
        'conversions' => 'integer',
        'clicks' => 'integer',
        'spend' => 'decimal:2',
    ];

    /**
     * Get platform enums for all assigned target platforms.
     *
     * @return array<MarketingPlatform>
     */
    public function getPlatformEnums(): array
    {
        $platforms = $this->platforms ?? ($this->platform ? [$this->platform] : []);
        if (! is_array($platforms)) {
            return [];
        }

        $enums = [];
        foreach ($platforms as $p) {
            $enum = $p instanceof MarketingPlatform ? $p : MarketingPlatform::tryFrom((string) $p);
            if ($enum) {
                $enums[] = $enum;
            }
        }

        return $enums;
    }

    /**
     * Synchronize legacy singular platform attribute whenever platforms are mutated.
     */
    public function setPlatformsAttribute($value): void
    {
        $array = is_array($value) ? array_values($value) : ($value ? [$value] : []);
        $normalized = array_map(fn ($p) => $p instanceof MarketingPlatform ? $p->value : (string) $p, $array);

        $this->attributes['platforms'] = json_encode($normalized);

        // Keep legacy single platform in sync with first selected platform
        $first = $normalized[0] ?? null;
        $this->attributes['platform'] = $first;
    }

    /**
     * Accessor for platforms array with legacy single platform fallback.
     */
    public function getPlatformsAttribute($value): array
    {
        if ($value) {
            $decoded = is_string($value) ? json_decode($value, true) : $value;
            if (is_array($decoded) && ! empty($decoded)) {
                return array_values($decoded);
            }
        }

        $legacy = $this->attributes['platform'] ?? null;

        return $legacy ? [(string) $legacy] : [];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
