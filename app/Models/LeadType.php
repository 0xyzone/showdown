<?php

namespace App\Models;

use Database\Factories\LeadTypeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeadType extends Model
{
    /** @use HasFactory<LeadTypeFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * Get all of the leads for the LeadType
     */
    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }
}
