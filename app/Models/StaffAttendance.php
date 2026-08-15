<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'date',
    'punch_in_at',
    'punch_out_at',
    'worked_minutes',
    'status',
    'location_mode',
    'punch_in_latitude',
    'punch_in_longitude',
    'punch_in_accuracy',
    'punch_in_distance_meters',
    'punch_in_ip',
    'punch_in_method',
    'punch_in_verified_biometric',
    'punch_out_latitude',
    'punch_out_longitude',
    'punch_out_accuracy',
    'punch_out_distance_meters',
    'punch_out_ip',
    'punch_out_method',
    'punch_out_verified_biometric',
    'is_manually_corrected',
    'correction_reason',
    'corrected_by',
    'corrected_at',
    'notes',
])]
class StaffAttendance extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'punch_in_at' => 'datetime',
            'punch_out_at' => 'datetime',
            'worked_minutes' => 'integer',
            'punch_in_latitude' => 'decimal:7',
            'punch_in_longitude' => 'decimal:7',
            'punch_in_accuracy' => 'decimal:2',
            'punch_in_distance_meters' => 'integer',
            'punch_in_verified_biometric' => 'boolean',
            'punch_out_latitude' => 'decimal:7',
            'punch_out_longitude' => 'decimal:7',
            'punch_out_accuracy' => 'decimal:2',
            'punch_out_distance_meters' => 'integer',
            'punch_out_verified_biometric' => 'boolean',
            'is_manually_corrected' => 'boolean',
            'corrected_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function correctedByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'corrected_by');
    }

    public function punchEvents(): HasMany
    {
        return $this->hasMany(StaffPunchEvent::class);
    }

    public function isCurrentlyWorking(): bool
    {
        return $this->punch_in_at !== null && $this->punch_out_at === null;
    }

    public function getFormattedWorkedTimeAttribute(): string
    {
        $minutes = $this->worked_minutes;
        if ($this->isCurrentlyWorking()) {
            $minutes = max(0, (int) round(now()->diffInMinutes($this->punch_in_at)));
        }

        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        return "{$hours}h {$remainingMinutes}m";
    }
}
