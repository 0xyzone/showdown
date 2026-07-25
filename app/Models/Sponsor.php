<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'tournament_id',
    'name',
    'logo_url',
    'website_url',
    'level',
    'order',
    'is_active',
    'sponsor_query_id',
])]
class Sponsor extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function sponsorQuery(): BelongsTo
    {
        return $this->belongsTo(SponsorQuery::class);
    }
}
