<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable([
    'ticket_purchase_id',
    'tournament_id',
    'ticket_package_id',
    'package_name',
    'ticket_number',
    'verification_token',
    'customer_name',
    'customer_phone',
    'price',
    'status',
    'is_used',
    'used_at',
    'verified_by',
    'verification_method',
])]
class Ticket extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_used' => 'boolean',
            'used_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Ticket $ticket) {
            if (empty($ticket->verification_token)) {
                $ticket->verification_token = (string) Str::uuid();
            }
            if (empty($ticket->ticket_number)) {
                $ticket->ticket_number = 'TCK-'.strtoupper(Str::random(4)).'-'.strtoupper(Str::random(4));
            }
        });
    }

    public function ticketPurchase(): BelongsTo
    {
        return $this->belongsTo(TicketPurchase::class);
    }

    public function ticketPackage(): BelongsTo
    {
        return $this->belongsTo(TicketPackage::class);
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function validEventDays(): BelongsToMany
    {
        return $this->belongsToMany(TournamentEventDay::class, 'ticket_event_day')->orderBy('order')->orderBy('event_date');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(TicketAttendance::class)->orderBy('verified_at', 'desc');
    }

    public function isCheckedInForDay(int|string|TournamentEventDay $eventDay): bool
    {
        $eventDayId = $eventDay instanceof TournamentEventDay ? $eventDay->id : $eventDay;

        return $this->attendances()->where('tournament_event_day_id', $eventDayId)->exists();
    }

    public function isValidForDay(int|string|TournamentEventDay $eventDay): bool
    {
        $eventDayId = $eventDay instanceof TournamentEventDay ? $eventDay->id : $eventDay;

        // If no explicit event days are linked to this ticket, it is valid for all tournament days by default
        if ($this->validEventDays()->count() === 0) {
            return true;
        }

        return $this->validEventDays()->where('tournament_event_days.id', $eventDayId)->exists();
    }

    public function getVerificationUrlAttribute(): string
    {
        return route('ticket.verify', ['token' => $this->verification_token]);
    }
}
