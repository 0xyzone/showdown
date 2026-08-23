<?php

namespace App\Models;

use Database\Factories\LeadFollowupFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadFollowup extends Model
{
    /** @use HasFactory<LeadFollowupFactory> */
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'user_id',
        'followup_date',
        'remarks',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'followup_date' => 'date',
        ];
    }

    /**
     * Get the lead that owns the followup.
     */
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    /**
     * Get the user that recorded the followup.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
