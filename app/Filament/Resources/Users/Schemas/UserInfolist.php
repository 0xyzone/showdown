<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Official Member Details')
                    ->schema([
                        Grid::make(3)->schema([
                            ImageEntry::make('avatar_url')
                                ->label('Profile Photo')
                                ->circular()
                                ->disk('public')
                                ->columnSpan(1),
                            TextEntry::make('name')
                                ->label('Full Name')
                                ->weight('bold'),
                            TextEntry::make('email')
                                ->label('Email Address')
                                ->icon('heroicon-m-envelope'),
                            TextEntry::make('username')
                                ->label('Username')
                                ->placeholder('-'),
                            TextEntry::make('roles.name')
                                ->label('Assigned Roles')
                                ->badge(),
                            IconEntry::make('is_active')
                                ->label('Active Status')
                                ->boolean(),
                        ]),
                    ]),
                Section::make('Contact & Social Information')
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('phone')
                                ->label('Primary Phone')
                                ->placeholder('-'),
                            TextEntry::make('alt_phone')
                                ->label('Alternate Phone')
                                ->placeholder('-'),
                            TextEntry::make('discord_id')
                                ->label('Discord ID')
                                ->placeholder('-'),
                            TextEntry::make('address')
                                ->label('Address')
                                ->placeholder('-'),
                        ]),
                    ]),
                Section::make('Verification & Documents')
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('citizenship_number')
                                ->label('Citizenship Number')
                                ->placeholder('-')
                                ->columnSpanFull(),
                            ImageEntry::make('citizenship_image')
                                ->label('Citizenship Document')
                                ->disk('public')
                                ->placeholder('No image uploaded'),
                            ImageEntry::make('qr_code_image')
                                ->label('QR Code')
                                ->disk('public')
                                ->placeholder('No image uploaded'),
                        ]),
                    ]),
            ]);
    }
}
