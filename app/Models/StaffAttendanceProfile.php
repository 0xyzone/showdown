<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

#[Fillable([
    'user_id',
    'attendance_mode',
    'is_biometric_exempt',
    'notes',
])]
class StaffAttendanceProfile extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_biometric_exempt' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function biometricCredentials(): HasManyThrough
    {
        return $this->hasManyThrough(
            StaffBiometricCredential::class,
            User::class,
            'id',
            'user_id',
            'user_id',
            'id'
        );
    }

    public function isRemoteAllowed(): bool
    {
        return in_array($this->attendance_mode, ['remote_allowed', 'flexible']);
    }

    public function isOfficeOnly(): bool
    {
        return $this->attendance_mode === 'office_only';
    }
}
