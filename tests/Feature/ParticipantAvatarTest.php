<?php

namespace Tests\Feature;

use App\Models\Participant;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ParticipantAvatarTest extends TestCase
{
    use RefreshDatabase;

    public function test_participant_implements_filament_user_and_has_avatar(): void
    {
        $participant = Participant::create([
            'name' => 'John Player',
            'email' => 'john@player.com',
            'password' => Hash::make('password'),
            'avatar_url' => 'avatars/test-participant-avatar.png',
        ]);

        $panel = Filament::getPanel('mukhyadwar');

        $this->assertTrue($participant->canAccessPanel($panel));

        Storage::fake('public');
        Storage::disk('public')->put('avatars/test-participant-avatar.png', 'avatar-content');

        $avatarUrl = $participant->getFilamentAvatarUrl();

        $this->assertNotNull($avatarUrl);
        $this->assertStringContainsString('avatars/test-participant-avatar.png', $avatarUrl);
    }

    public function test_participant_avatar_url_and_avatar_path_synchronization(): void
    {
        $participant = Participant::create([
            'name' => 'Jane Player',
            'email' => 'jane@player.com',
            'password' => Hash::make('password'),
            'avatar_url' => 'avatars/sample.png',
        ]);

        $this->assertEquals('avatars/sample.png', $participant->avatar_path);
        $this->assertEquals('avatars/sample.png', $participant->avatar_url);

        $participant->update([
            'avatar_path' => 'avatars/updated.png',
            'avatar_url' => null,
        ]);

        $this->assertEquals('avatars/updated.png', $participant->avatar_url);
    }
}
