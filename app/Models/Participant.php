<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;

#[Fillable([
    'name',
    'email',
    'password',
    'phone',
    'avatar_path',
    'avatar_url',
    'ign',
    'discord_tag',
    'role',
    'bio',
])]
class Participant extends Authenticatable implements FilamentUser, HasAvatar
{
    use HasFactory, HasRoles, Notifiable;

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Participant $participant) {
            if ($participant->avatar_url && ! $participant->avatar_path) {
                $participant->avatar_path = $participant->avatar_url;
            } elseif ($participant->avatar_path && ! $participant->avatar_url) {
                $participant->avatar_url = $participant->avatar_path;
            }
        });
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $panel->getId() === 'mukhyadwar';
    }

    public function getFilamentAvatarUrl(): ?string
    {
        $avatar = $this->avatar_url ?? $this->avatar_path;

        if (! $avatar) {
            return null;
        }

        if (str_starts_with($avatar, 'http://') || str_starts_with($avatar, 'https://')) {
            return $avatar;
        }

        return Storage::disk('public')->url($avatar);
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class, 'manager_id');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(TournamentRegistration::class, 'registered_by');
    }
}
