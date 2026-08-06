<?php

namespace App\Filament\Resources\Leads\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('user_id')
                    ->default(auth()->id()),
                Section::make('Type & Status')
                    ->schema([
                        Select::make('lead_type_id')
                            ->relationship('lead_type', 'name')
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required(),
                            ])
                            ->required(),
                        Select::make('lead_status_id')
                            ->relationship('lead_status', 'name')
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required(),
                            ])
                            ->required(),
                    ])
                    ->columnSpanFull()
                    ->columns(2),
                Section::make('Company Details')
                    ->schema([
                        TextInput::make('company_name')
                            ->required(),
                        TextInput::make('contact_name')
                            ->required(),
                        TextInput::make('phone')
                            ->tel()
                            ->required(),
                        TextInput::make('email')
                            ->label('Email address')
                            ->email(),
                    ])
                    ->columnSpanFull()
                    ->columns(2),
                Section::make('Location/Address')
                    ->schema([
                        Textarea::make('address')
                            ->label('Location/Address')
                            ->autoSize(),
                        TextInput::make('gmap_link')
                            ->label('Google Maps Link')
                            ->url(),
                    ])
                    ->columnSpanFull()
                    ->columns(2),
                Section::make('Notes')
                    ->schema([
                        Textarea::make('notes')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
