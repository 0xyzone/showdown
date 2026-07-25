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
                Section::make('Official Brand Identity')
                    ->description('Specify the official brand name, assigned tournament, website URL, and graphic logo asset.')
                    ->icon('heroicon-o-sparkles')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('name')
                                ->label('Brand / Sponsor Name')
                                ->placeholder('e.g. Monster Energy, Razer Gaming')
                                ->required()
                                ->maxLength(255),
                            Select::make('tournament_id')
                                ->label('Assigned Tournament')
                                ->relationship('tournament', 'name')
                                ->nullable()
                                ->placeholder('All Tournaments (Global)'),
                            TextInput::make('website_url')
                                ->label('Website / Landing URL')
                                ->url()
                                ->placeholder('https://example.com')
                                ->maxLength(255),
                            FileUpload::make('logo_url')
                                ->label('Brand Logo Graphic')
                                ->image()
                                ->imageEditor()
                                ->disk('public')
                                ->directory('sponsors')
                                ->visibility('public')
                                ->required()
                                ->columnSpanFull(),
                        ]),
                    ]),

                Section::make('Hierarchy & Placement Settings')
                    ->description('Configure tier level hierarchy, sort position, and active visibility on the website.')
                    ->icon('heroicon-o-adjustments-vertical')
                    ->schema([
                        Grid::make(3)->schema([
                            Select::make('level')
                                ->label('Sponsorship Hierarchy Tier')
                                ->options([
                                    'title' => '👑 Title Sponsor (Mega Spotlight Banner)',
                                    'platinum' => '💎 Platinum Sponsor (Large 2-Col Grid)',
                                    'gold' => '🥇 Gold Sponsor (Medium Grid)',
                                    'silver' => '🛡️ Silver Sponsor (Standard Stream)',
                                ])
                                ->required()
                                ->default('silver'),
                            TextInput::make('order')
                                ->label('Display Sort Order')
                                ->numeric()
                                ->default(0),
                            Toggle::make('is_active')
                                ->label('Visible on Website')
                                ->helperText('Toggle to immediately show/hide this sponsor on the homepage.')
                                ->default(true),
                        ]),
                    ]),
            ]);
    }
}
