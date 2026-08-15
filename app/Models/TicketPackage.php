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
    'name',
    'description',
    'price',
    'validity_type',
    'is_active',
    'order',
])]
class TicketPackage extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
            'order' => 'integer',
        ];
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function eventDays(): BelongsToMany
    {
        return $this->belongsToMany(TournamentEventDay::class, 'ticket_package_event_day');
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(TicketPurchase::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rs. '.number_format($this->price, 2);
    }
}
