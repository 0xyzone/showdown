<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Storage;

class UserObserver
{
    /**
     * File upload fields on the User model to monitor for replacement/deletion.
     *
     * @var array<int, string>
     */
    protected array $fileFields = [
        'avatar_url',
        'citizenship_image',
        'qr_code_image',
    ];

    /**
     * Handle the User "updating" event.
     */
    public function updating(User $user): void
    {
        // Automatically clear must_change_password flag when user changes password
        if ($user->isDirty('password')) {
            $user->must_change_password = false;
        }

        $disk = Storage::disk(config('filament-edit-profile.disk', 'public'));

        foreach ($this->fileFields as $field) {
            if ($user->isDirty($field)) {
                $oldFile = $user->getOriginal($field);

                if ($oldFile && $disk->exists($oldFile)) {
                    $disk->delete($oldFile);
                }
            }
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        $disk = Storage::disk(config('filament-edit-profile.disk', 'public'));

        foreach ($this->fileFields as $field) {
            $file = $user->$field;

            if ($file && $disk->exists($file)) {
                $disk->delete($file);
            }
        }
    }
}
