<?php

namespace App\Models;

use App\Observers\LeadMeetingObserver;
use Database\Factories\LeadMeetingFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy([LeadMeetingObserver::class])]
class LeadMeeting extends Model
{
    /** @use HasFactory<LeadMeetingFactory> */
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'user_id',
        'title',
        'meeting_start',
        'meeting_end',
        'meeting_location_type',
        'meeting_link',
        'google_event_id',
        'notes',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'meeting_start' => 'datetime',
            'meeting_end' => 'datetime',
        ];
    }

    /**
     * Get the lead that owns the meeting.
     */
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    /**
     * Get the user who scheduled/hosts the meeting.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
