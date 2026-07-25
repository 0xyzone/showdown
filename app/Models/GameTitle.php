<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable([
    'name',
    'slug',
    'developer',
    'logo_path',
    'banner_path',
    'game_type',
])]
class GameTitle extends Model
{
    use HasFactory;

    public function tournaments(): BelongsToMany
    {
        return $this->belongsToMany(Tournament::class);
    }
}
