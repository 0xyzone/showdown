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

    public function canAccessPanel(Panel $panel): bool
    {
        return $panel->getId() === 'mukhyadwar';
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_path ? Storage::disk('public')->url($this->avatar_path) : null;
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
