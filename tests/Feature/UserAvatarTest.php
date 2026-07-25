<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserAvatarTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_returns_filament_avatar_url(): void
    {
        $disk = config('filament-edit-profile.disk', 'public');
        Storage::fake($disk);

        $user = User::factory()->create([
            'avatar_url' => 'avatars/avatar1.jpg',
        ]);

        $avatarUrl = $user->getFilamentAvatarUrl();

        $this->assertNotNull($avatarUrl);
        $this->assertStringContainsString('avatars/avatar1.jpg', $avatarUrl);
    }

    public function test_observer_deletes_old_avatar_on_update(): void
    {
        $diskName = config('filament-edit-profile.disk', 'public');
        Storage::fake($diskName);

        $file1 = UploadedFile::fake()->image('avatar1.jpg')->store('avatars', $diskName);
        $user = User::factory()->create([
            'avatar_url' => $file1,
        ]);

        Storage::disk($diskName)->assertExists($file1);

        $file2 = UploadedFile::fake()->image('avatar2.jpg')->store('avatars', $diskName);
        $user->update(['avatar_url' => $file2]);

        Storage::disk($diskName)->assertMissing($file1);
        Storage::disk($diskName)->assertExists($file2);
    }

    public function test_observer_deletes_avatar_on_user_deletion(): void
    {
        $diskName = config('filament-edit-profile.disk', 'public');
        Storage::fake($diskName);

        $file = UploadedFile::fake()->image('avatar.jpg')->store('avatars', $diskName);
        $user = User::factory()->create([
            'avatar_url' => $file,
        ]);

        Storage::disk($diskName)->assertExists($file);

        $user->delete();

        Storage::disk($diskName)->assertMissing($file);
    }
}
