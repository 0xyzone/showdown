<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'team_id',
    'full_name',
    'role',
    'date_of_birth',
    'front_photo_path',
    'ign',
    'ingame_role',
    'whatsapp_number',
    'email',
    'discord_id',
    'citizenship_number',
    'citizenship_front_path',
    'citizenship_back_path',
    'ingame_profile_screenshot_path',
])]
class TeamPlayer extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
        ];
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
