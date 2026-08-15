<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'name',
    'credential_id',
    'public_key',
    'counter',
    'aaguid',
    'attestation_type',
    'transports',
    'device_type',
    'last_used_at',
    'is_active',
])]
class StaffBiometricCredential extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'counter' => 'integer',
            'transports' => 'array',
            'last_used_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
