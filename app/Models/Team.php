<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'manager_id',
    'name',
    'tag',
    'logo_path',
    'country',
])]
class Team extends Model
{
    use HasFactory;

    public function manager(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'manager_id');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(TournamentRegistration::class);
    }

    public function standings(): HasMany
    {
        return $this->hasMany(TournamentStandings::class);
    }
}
