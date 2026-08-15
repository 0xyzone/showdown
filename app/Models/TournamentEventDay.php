<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'tournament_id',
    'day_name',
    'event_date',
    'order',
    'is_active',
    'notes',
])]
class TournamentEventDay extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function ticketPackages(): BelongsToMany
    {
        return $this->belongsToMany(TicketPackage::class, 'ticket_package_event_day');
    }

    public function tickets(): BelongsToMany
    {
        return $this->belongsToMany(Ticket::class, 'ticket_event_day');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(TicketAttendance::class);
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->event_date ? $this->event_date->format('M d, Y') : '';
    }

    public function getDisplayNameAttribute(): string
    {
        return "{$this->day_name} ({$this->formatted_date})";
    }
}
