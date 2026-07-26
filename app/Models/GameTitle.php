<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'slug',
    'developer',
    'logo_path',
    'banner_path',
    'game_type',
    'min_main_players',
    'max_substitutes',
])]
class GameTitle extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'min_main_players' => 'integer',
            'max_substitutes' => 'integer',
        ];
    }

    public function tournaments(): BelongsToMany
    {
        return $this->belongsToMany(Tournament::class);
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }
}
