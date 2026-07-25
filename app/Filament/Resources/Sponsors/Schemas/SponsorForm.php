<?php

namespace App\Filament\Resources\Sponsors\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SponsorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Official Sponsor Information')
                    ->description('Specify the branding logo, hierarchy tier, and display active status.')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Brand / Sponsor Name')
                                ->required()
                                ->maxLength(255),
                            Select::make('level')
                                ->label('Sponsorship Hierarchy Tier')
                                ->options([
                                    'title' => 'Title Sponsor (Mega Spotlight)',
                                    'platinum' => 'Platinum Sponsor (Large Spotlight)',
                                    'gold' => 'Gold Sponsor (Medium Spotlight)',
                                    'silver' => 'Silver Sponsor (Standard Stream)',
                                ])
                                ->required()
                                ->default('silver'),
                            TextInput::make('website_url')
                                ->label('Website / Landing URL')
                                ->url()
                                ->placeholder('https://')
                                ->maxLength(255),
                            TextInput::make('order')
                                ->label('Display Sort Order')
                                ->numeric()
                                ->default(0),
                            FileUpload::make('logo_url')
                                ->label('Brand Logo Graphic')
                                ->image()
                                ->disk('public')
                                ->directory('sponsors')
                                ->visibility('public')
                                ->required()
                                ->columnSpanFull(),
                            Toggle::make('is_active')
                                ->label('Visible on Website')
                                ->default(true),
                        ]),
                    ]),
            ]);
    }
}
