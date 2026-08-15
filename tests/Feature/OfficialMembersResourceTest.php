<?php

namespace Tests\Feature;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OfficialMembersResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'super_admin']);
        Role::create(['name' => 'staff']);
    }

    public function test_super_admin_can_render_official_members_list_page(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        $members = User::factory()->count(3)->create();

        Livewire::actingAs($admin)
            ->test(ListUsers::class)
            ->assertCanSeeTableRecords($members);
    }

    public function test_super_admin_can_create_official_member(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        Livewire::actingAs($admin)
            ->test(CreateUser::class)
            ->fillForm([
                'name' => 'New Member',
                'email' => 'newmember@example.com',
                'password' => 'secret123',
                'username' => 'newmember',
                'phone' => '9800000000',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas(User::class, [
            'email' => 'newmember@example.com',
            'username' => 'newmember',
        ]);

        $newMember = User::where('email', 'newmember@example.com')->first();
        $this->assertNotNull($newMember);
        $this->assertTrue($newMember->hasRole('staff'));
        $this->assertNotNull($newMember->attendanceProfile);
    }

    public function test_super_admin_can_edit_official_member(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        $member = User::factory()->create(['name' => 'Old Name']);

        Livewire::actingAs($admin)
            ->test(EditUser::class, ['record' => $member->getRouteKey()])
            ->fillForm([
                'name' => 'Updated Member Name',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas(User::class, [
            'id' => $member->id,
            'name' => 'Updated Member Name',
        ]);
    }
}
