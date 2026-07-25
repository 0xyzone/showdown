<?php

namespace App\Filament\Resources\Partners\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PartnerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Official Partner Details')
                    ->description('Define partner title categories (e.g., Media Partner, Hospitality Partner) and logo display settings.')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Partner / Brand Name')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('title')
                                ->label('Partner Category Title')
                                ->placeholder('e.g. Media Partner, Hospitality Partner, Beverage Partner')
                                ->required()
                                ->maxLength(255),
                            Select::make('level')
                                ->label('Partner Hierarchy Tier')
                                ->options([
                                    'major' => 'Major Partner',
                                    'standard' => 'Standard Partner',
                                ])
                                ->required()
                                ->default('standard'),
                            TextInput::make('website_url')
                                ->label('Website / Landing URL')
                                ->url()
                                ->placeholder('https://')
                                ->maxLength(255),
                            TextInput::make('order')
                                ->label('Display Sort Order')
                                ->numeric()
                                ->default(0),
                            Toggle::make('is_active')
                                ->label('Visible on Website')
                                ->default(true),
                            FileUpload::make('logo_url')
                                ->label('Brand Logo Graphic')
                                ->image()
                                ->disk('public')
                                ->directory('partners')
                                ->visibility('public')
                                ->required()
                                ->columnSpanFull(),
                        ]),
                    ]),
            ]);
    }
}
