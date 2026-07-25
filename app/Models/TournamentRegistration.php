<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'tournament_id',
    'team_id',
    'registered_by',
    'status',
    'roster_data',
    'payment_receipt_path',
    'notes',
])]
class TournamentRegistration extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'roster_data' => 'array',
        ];
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'registered_by');
    }
}
