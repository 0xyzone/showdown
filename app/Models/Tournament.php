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
    'season_version',
    'logo_path',
    'banner_path',
    'description',
    'hero_headline',
    'hero_subheadline',
    'rules_doc_link',
    'challonge_url',
    'challonge_embed_url',
    'discord_server_url',
    'discord_webhook_url',
    'linktree_url',
    'custom_links',
    'start_date',
    'end_date',
    'registration_start',
    'registration_end',
    'status',
    'is_active',
    'theme_color',
    'prize_pool_total',
    'entry_fee',
])]
class Tournament extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'custom_links' => 'array',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'registration_start' => 'datetime',
            'registration_end' => 'datetime',
            'prize_pool_total' => 'decimal:2',
            'entry_fee' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Tournament $tournament) {
            if ($tournament->is_active) {
                static::where('id', '!=', $tournament->id ?? 0)->update(['is_active' => false]);
            } else {
                $otherActiveExists = static::where('id', '!=', $tournament->id ?? 0)->where('is_active', true)->exists();
                if (! $otherActiveExists) {
                    $tournament->is_active = true;
                }
            }
        });
    }

    public function gameTitles(): BelongsToMany
    {
        return $this->belongsToMany(GameTitle::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(TournamentRegistration::class);
    }

    public function sponsors(): HasMany
    {
        return $this->hasMany(Sponsor::class);
    }

    public function partners(): HasMany
    {
        return $this->hasMany(Partner::class);
    }
}
