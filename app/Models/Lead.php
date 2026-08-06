<?php

namespace App\Models;

use App\Models\LeadStatus;
use App\Models\LeadType;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lead extends Model
{
    /**
     * Get the user that owns the Lead
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the lead_type that owns the Lead
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function lead_type(): BelongsTo
    {
        return $this->belongsTo(LeadType::class);
    }

    /**
     * Get the lead_status that owns the Lead
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function lead_status(): BelongsTo
    {
        return $this->belongsTo(LeadStatus::class);
    }
}
