<?php

namespace App\Models;

use App\Models\Lead;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeadType extends Model
{
    /**
     * Get all of the leads for the LeadType
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }
}
