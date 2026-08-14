<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeadType extends Model
{
    /**
     * Get all of the leads for the LeadType
     */
    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }
}
