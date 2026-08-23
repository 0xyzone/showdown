<?php

namespace App\Models;

use Database\Factories\LeadFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Lead extends Model
{
    /** @use HasFactory<LeadFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'lead_type_id',
        'lead_status_id',
        'company_name',
        'contact_name',
        'phone',
        'email',
        'address',
        'gmap_link',
        'notes',
    ];

    /**
     * Get the user that owns the Lead
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the lead_type that owns the Lead
     */
    public function lead_type(): BelongsTo
    {
        return $this->belongsTo(LeadType::class);
    }

    /**
     * Get the lead_status that owns the Lead
     */
    public function lead_status(): BelongsTo
    {
        return $this->belongsTo(LeadStatus::class);
    }

    /**
     * Get the followups for the Lead
     */
    public function followups(): HasMany
    {
        return $this->hasMany(LeadFollowup::class)->orderBy('followup_date', 'desc')->orderBy('id', 'desc');
    }

    /**
     * Get the most recent followup for the Lead
     */
    public function latestFollowup(): HasOne
    {
        return $this->hasOne(LeadFollowup::class)->latestOfMany('followup_date');
    }
}
