<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeadStatus extends Model
{
    /**
     * Get all of the leads for the LeadStatus
     */
    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }
}
