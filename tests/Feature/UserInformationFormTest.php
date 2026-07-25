<?php

namespace Tests\Feature;

use App\Livewire\UserInformationForm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UserInformationFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_save_additional_profile_information(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(UserInformationForm::class)
            ->fillForm([
                'phone' => '1234567890',
                'alt_phone' => '0987654321',
                'discord_id' => 'john#1234',
                'address' => '123 Main St',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas(User::class, [
            'id' => $user->id,
            'phone' => '1234567890',
            'alt_phone' => '0987654321',
            'discord_id' => 'john#1234',
            'address' => '123 Main St',
        ]);
    }
}
