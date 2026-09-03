<?php

namespace App\Models;

use App\Enums\CampaignPriority;
use App\Enums\CampaignStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Campaign extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'campaign_code',
        'objectives',
        'target_audience',
        'budget',
        'actual_spend',
        'start_date',
        'end_date',
        'status',
        'priority',
        'platforms',
        'tags',
        'owner_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'budget' => 'decimal:2',
        'actual_spend' => 'decimal:2',
        'status' => CampaignStatus::class,
        'priority' => CampaignPriority::class,
        'platforms' => 'array',
        'tags' => 'array',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function teamMembers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'campaign_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function deliverables(): HasMany
    {
        return $this->hasMany(CampaignDeliverable::class);
    }

    public function durationInDays(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->start_date && $this->end_date
                ? Carbon::parse($this->start_date)->diffInDays(Carbon::parse($this->end_date)) + 1
                : 0
        );
    }

    public function budgetUtilizationPercentage(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->budget || $this->budget <= 0) {
                    return 0;
                }

                return min(100, round(($this->actual_spend / $this->budget) * 100, 1));
            }
        );
    }

    public function totalImpressions(): Attribute
    {
        return Attribute::make(
            get: fn () => (int) $this->deliverables()->sum('impressions')
        );
    }

    public function totalReach(): Attribute
    {
        return Attribute::make(
            get: fn () => (int) $this->deliverables()->sum('reach')
        );
    }

    public function totalConversions(): Attribute
    {
        return Attribute::make(
            get: fn () => (int) $this->deliverables()->sum('conversions')
        );
    }
}
