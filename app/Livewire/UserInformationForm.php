<?php

namespace App\Livewire;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Exceptions\Halt;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rules\Unique;
use Joaopaulolndev\FilamentEditProfile\Concerns\HasSort;
use Livewire\Component;

class UserInformationForm extends Component implements HasForms
{
    use HasSort;
    use InteractsWithForms;

    public ?array $data = [];

    protected static int $sort = 10;

    public function mount(): void
    {
        $this->form->fill(
            auth()->user()?->only([
                'username',
                'phone',
                'alt_phone',
                'discord_id',
                'address',
                'citizenship_number',
                'citizenship_image',
                'qr_code_image',
            ])
        );
    }

    public function form(Schema $schema): Schema
    {
        $userId = auth()->id();

        return $schema
            ->components([
                Section::make('Additional Profile Details')
                    ->aside()
                    ->description('Update your contact, identification, and profile details.')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('username')
                                ->label('Username')
                                ->unique(ignoreRecord: true, modifyRuleUsing: fn (Unique $rule) => $rule->ignore($userId))
                                ->maxLength(255),
                            TextInput::make('discord_id')
                                ->label('Discord ID')
                                ->unique(ignoreRecord: true, modifyRuleUsing: fn (Unique $rule) => $rule->ignore($userId))
                                ->maxLength(255),
                            TextInput::make('phone')
                                ->label('Phone Number')
                                ->tel()
                                ->unique(ignoreRecord: true, modifyRuleUsing: fn (Unique $rule) => $rule->ignore($userId))
                                ->maxLength(255),
                            TextInput::make('alt_phone')
                                ->label('Alternative Phone')
                                ->tel()
                                ->unique(ignoreRecord: true, modifyRuleUsing: fn (Unique $rule) => $rule->ignore($userId))
                                ->maxLength(255),
                            TextInput::make('address')
                                ->label('Address')
                                ->columnSpanFull()
                                ->maxLength(255),
                            TextInput::make('citizenship_number')
                                ->label('Citizenship Number')
                                ->unique(ignoreRecord: true, modifyRuleUsing: fn (Unique $rule) => $rule->ignore($userId))
                                ->disabled(fn ($state) => $state)
                                ->columnSpanFull()
                                ->maxLength(255),
                            FileUpload::make('citizenship_image')
                                ->label('Citizenship Image')
                                ->image()
                                ->disk('public')
                                ->directory('citizenship-images')
                                ->visibility('public')
                                ->disabled(fn ($state) => $state)
                                ->columnSpan(1),
                            FileUpload::make('qr_code_image')
                                ->label('QR Code Image')
                                ->image()
                                ->disk('public')
                                ->directory('qr-code-images')
                                ->visibility('public')
                                ->columnSpan(1),
                        ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();

            auth()->user()?->update($data);
        } catch (Halt $exception) {
            return;
        }

        Notification::make()
            ->success()
            ->title('Profile details saved successfully.')
            ->send();
    }

    public function render(): View
    {
        return view('livewire.user-information-form');
    }
}
