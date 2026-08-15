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
    'ticket_price',
    'entry_fee',
    'entry_fee_suffix',
    'min_main_players',
    'max_main_players',
    'max_substitutes',
    'require_coach',
    'require_manager',
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
            'ticket_price' => 'decimal:2',
            'entry_fee' => 'decimal:2',
            'is_active' => 'boolean',
            'min_main_players' => 'integer',
            'max_main_players' => 'integer',
            'max_substitutes' => 'integer',
            'require_coach' => 'boolean',
            'require_manager' => 'boolean',
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

    public function getFormattedEntryFeeAttribute(): string
    {
        $amount = number_format($this->entry_fee, 0);
        $suffix = $this->entry_fee_suffix ? "/{$this->entry_fee_suffix}" : '/person';

        return "Rs. {$amount}{$suffix}";
    }

    public function gameTitles(): BelongsToMany
    {
        return $this->belongsToMany(GameTitle::class)
            ->withPivot(['prize_pool', 'prize_distribution'])
            ->withTimestamps();
    }

    public function tournamentRegistrations(): HasMany
    {
        return $this->hasMany(TournamentRegistration::class);
    }

    public function ticketPurchases(): HasMany
    {
        return $this->hasMany(TicketPurchase::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function eventDays(): HasMany
    {
        return $this->hasMany(TournamentEventDay::class)->orderBy('order')->orderBy('event_date');
    }

    public function ticketPackages(): HasMany
    {
        return $this->hasMany(TicketPackage::class)->orderBy('order');
    }

    public function paymentMethods(): BelongsToMany
    {
        return $this->belongsToMany(PaymentMethod::class);
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
