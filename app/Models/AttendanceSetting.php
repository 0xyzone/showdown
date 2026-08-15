<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'office_name',
    'office_latitude',
    'office_longitude',
    'allowed_radius_meters',
    'max_gps_accuracy_meters',
    'is_network_restriction_enabled',
    'allowed_ip_addresses',
    'require_biometric',
    'max_devices_per_user',
])]
class AttendanceSetting extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'office_latitude' => 'decimal:7',
            'office_longitude' => 'decimal:7',
            'allowed_radius_meters' => 'integer',
            'max_gps_accuracy_meters' => 'integer',
            'is_network_restriction_enabled' => 'boolean',
            'allowed_ip_addresses' => 'array',
            'require_biometric' => 'boolean',
            'max_devices_per_user' => 'integer',
        ];
    }

    public static function current(): self
    {
        return static::firstOrCreate(
            ['id' => 1],
            [
                'office_name' => 'Showdown Headquarters',
                'office_latitude' => 27.7172453,
                'office_longitude' => 85.3239605,
                'allowed_radius_meters' => 150,
                'max_gps_accuracy_meters' => 100,
                'is_network_restriction_enabled' => false,
                'allowed_ip_addresses' => [],
                'require_biometric' => true,
                'max_devices_per_user' => 3,
            ]
        );
    }
}
