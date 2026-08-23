<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Observers\UserObserver;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;

#[Fillable([
    'name',
    'email',
    'password',
    'must_change_password',
    'avatar_url',
    'username',
    'phone',
    'alt_phone',
    'discord_id',
    'address',
    'citizenship_number',
    'citizenship_image',
    'qr_code_image',
    'custom_fields',
    'google_calendar_token',
    'google_calendar_connected_at',
])]
#[Hidden(['password', 'remember_token'])]
#[ObservedBy([UserObserver::class])]
class User extends Authenticatable implements FilamentUser, HasAvatar
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * Get the user's avatar URL for Filament.
     */
    public function getFilamentAvatarUrl(): ?string
    {
        $avatarColumn = config('filament-edit-profile.avatar_column', 'avatar_url');
        $avatar = $this->getAttribute($avatarColumn);

        if (! $avatar) {
            return null;
        }

        if (filter_var($avatar, FILTER_VALIDATE_URL)) {
            return $avatar;
        }

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk(config('filament-edit-profile.disk', 'public'));

        return $disk->url($avatar);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'must_change_password' => 'boolean',
            'custom_fields' => 'array',
            'google_calendar_token' => 'array',
            'google_calendar_connected_at' => 'datetime',
        ];
    }

    /**
     * Determine if user has connected their Google Calendar.
     */
    public function isGoogleCalendarConnected(): bool
    {
        return ! empty($this->google_calendar_token) && ! empty($this->google_calendar_token['refresh_token'] ?? $this->google_calendar_token['access_token'] ?? null);
    }

    /**
     * Get the connected Google account email.
     */
    public function getGoogleCalendarEmail(): ?string
    {
        return $this->google_calendar_token['email'] ?? null;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $panel->getId() === 'maidan';
    }

    /**
     * Get all of the leads for the User
     */
    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function ticketSales(): HasMany
    {
        return $this->hasMany(TicketPurchase::class, 'seller_id');
    }

    public function verifiedTickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'verified_by');
    }

    public function ticketAttendances(): HasMany
    {
        return $this->hasMany(TicketAttendance::class, 'verified_by');
    }

    public function attendanceProfile(): HasOne
    {
        return $this->hasOne(StaffAttendanceProfile::class);
    }

    public function biometricCredentials(): HasMany
    {
        return $this->hasMany(StaffBiometricCredential::class);
    }

    public function staffAttendances(): HasMany
    {
        return $this->hasMany(StaffAttendance::class);
    }

    public function staffPunchEvents(): HasMany
    {
        return $this->hasMany(StaffPunchEvent::class);
    }

    public function todayAttendance(): ?StaffAttendance
    {
        return $this->staffAttendances()->whereDate('date', today())->first();
    }
}
