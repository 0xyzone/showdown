<?php

namespace App\Filament\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ForcePasswordChange extends Page implements HasForms
{
    use HasPageShield;
    use InteractsWithForms;

    protected string $view = 'filament.pages.force-password-change';

    protected static ?string $title = 'Change Your Password';

    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public function mount(): void
    {
        $user = Auth::user();

        if (! $user || ! $user->must_change_password) {
            redirect()->to('/maidan');

            return;
        }

        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                TextInput::make('current_password')
                    ->label('Current / Auto-generated Password')
                    ->password()
                    ->revealable()
                    ->required()
                    ->currentPassword(),

                TextInput::make('password')
                    ->label('New Password')
                    ->revealable()
                    ->password()
                    ->required()
                    ->rule(Password::default())
                    ->same('password_confirmation'),

                TextInput::make('password_confirmation')
                    ->label('Confirm New Password')
                    ->revealable()
                    ->password()
                    ->required(),
            ]);
    }

    public function updatePassword(): void
    {
        $data = $this->form->getState();

        $user = Auth::user();

        $user->forceFill([
            'password' => Hash::make($data['password']),
            'must_change_password' => false,
        ])->save();

        Notification::make()
            ->title('Password updated successfully!')
            ->body('You may now access the administrative panel.')
            ->success()
            ->send();

        $this->redirect('/maidan');
    }
}
