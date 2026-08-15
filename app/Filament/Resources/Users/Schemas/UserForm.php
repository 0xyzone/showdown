<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Account Details')
                    ->description('Manage primary login credentials and assigned roles.')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Full Name')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('email')
                                ->label('Email Address')
                                ->email()
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->maxLength(255),
                            TextInput::make('password')
                                ->label('Password')
                                ->password()
                                ->helperText(fn (string $operation): ?string => $operation === 'create' ? 'Password will be automatically generated and emailed to the member.' : 'Leave empty to keep the existing password.')
                                ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                                ->dehydrated(fn ($state) => filled($state))
                                ->visible(fn (string $operation): bool => $operation !== 'create')
                                ->maxLength(255),
                            Select::make('roles')
                                ->relationship('roles', 'name')
                                ->multiple()
                                ->preload()
                                ->searchable()
                                ->default(function () {
                                    $staff = Role::where('name', 'staff')->where('guard_name', 'web')->first();

                                    return $staff ? [$staff->id] : [];
                                }),
                            Toggle::make('is_active')
                                ->label('Active Account Status')
                                ->default(true)
                                ->columnSpanFull(),
                        ]),
                    ]),
                Section::make('Member Profile')
                    ->description('Contact info and social identity parameters.')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('username')
                                ->label('Username')
                                ->unique(ignoreRecord: true)
                                ->maxLength(255),
                            TextInput::make('discord_id')
                                ->label('Discord ID')
                                ->unique(ignoreRecord: true)
                                ->maxLength(255),
                            TextInput::make('phone')
                                ->label('Primary Phone')
                                ->tel()
                                ->unique(ignoreRecord: true)
                                ->maxLength(255),
                            TextInput::make('alt_phone')
                                ->label('Alternate Phone')
                                ->tel()
                                ->unique(ignoreRecord: true)
                                ->maxLength(255),
                            TextInput::make('address')
                                ->label('Residential Address')
                                ->columnSpanFull()
                                ->maxLength(255),
                        ]),
                    ]),
                Section::make('Verification & Documents')
                    ->description('Legal identification documents and images.')
                    ->schema([
                        Grid::make(3)->schema([
                            FileUpload::make('avatar_url')
                                ->label('Profile Photo')
                                ->avatar()
                                ->disk('public')
                                ->directory('avatars')
                                ->visibility('public')
                                ->columnSpan(1),
                            FileUpload::make('citizenship_image')
                                ->label('Citizenship Document')
                                ->image()
                                ->disk('public')
                                ->directory('citizenship-images')
                                ->visibility('public')
                                ->columnSpan(1),
                            FileUpload::make('qr_code_image')
                                ->label('QR Code')
                                ->image()
                                ->disk('public')
                                ->directory('qr-code-images')
                                ->visibility('public')
                                ->columnSpan(1),
                            TextInput::make('citizenship_number')
                                ->label('Citizenship Number')
                                ->unique(ignoreRecord: true)
                                ->columnSpanFull()
                                ->maxLength(255),
                        ]),
                    ]),
            ]);
    }
}
