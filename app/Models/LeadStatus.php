<?php

namespace App\Models;

use Database\Factories\LeadStatusFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeadStatus extends Model
{
    /** @use HasFactory<LeadStatusFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * Get all of the leads for the LeadStatus
     */
    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }
}
