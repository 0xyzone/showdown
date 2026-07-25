<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'name',
    'title',
    'logo_url',
    'website_url',
    'level',
    'order',
    'is_active',
    'sponsor_query_id',
])]
class Partner extends Model
{
    use HasFactory;

    public function sponsorQuery(): BelongsTo
    {
        return $this->belongsTo(SponsorQuery::class, 'sponsor_query_id');
    }
}
