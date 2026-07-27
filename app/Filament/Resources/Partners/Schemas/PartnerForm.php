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
                Section::make('Partner Identity & Categorization')
                    ->description('Specify the official partner name, assigned tournament, and specific title category.')
                    ->icon('heroicon-o-user-group')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('name')
                                ->label('Partner / Brand Name')
                                ->placeholder('e.g. Kantipur TV Network, Hotel Annapurna')
                                ->required()
                                ->maxLength(255),
                            Select::make('tournament_id')
                                ->label('Assigned Tournament')
                                ->relationship('tournament', 'name')
                                ->nullable()
                                ->placeholder('All Tournaments (Global)'),
                            TextInput::make('title')
                                ->label('Partner Category Title')
                                ->placeholder('e.g. Media Partner, Hospitality Partner, Broadcasting Partner')
                                ->required()
                                ->maxLength(255),
                        ]),
                    ]),

                Section::make('Partnership Tier & Visibility')
                    ->description('Configure hierarchy tier, sort sequence, website link, and active visibility.')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->schema([
                        Grid::make(3)->schema([
                            Select::make('level')
                                ->label('Partnership Level')
                                ->options([
                                    'major' => '🌟 Major Partner',
                                    'standard' => '🤝 Standard Partner',
                                ])
                                ->required()
                                ->default('standard'),
                            TextInput::make('website_url')
                                ->label('Website / Landing URL')
                                ->url()
                                ->placeholder('https://example.com')
                                ->maxLength(255),
                            TextInput::make('order')
                                ->label('Display Sort Order')
                                ->numeric()
                                ->default(0),
                            Toggle::make('is_active')
                                ->label('Visible on Website')
                                ->helperText('Toggle to immediately show/hide this partner on the homepage.')
                                ->default(true)
                                ->columnSpanFull(),
                        ]),
                    ]),

                Section::make('Brand Graphic Asset')
                    ->icon('heroicon-o-photo')
                    ->schema([
                        FileUpload::make('logo_url')
                            ->label('Brand Logo Graphic (1:1 Ratio, PNG)')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '1:1',
                            ])
                            ->acceptedFileTypes(['image/png'])
                            ->imageCropAspectRatio('1:1')
                            ->disk('public')
                            ->directory('partners')
                            ->visibility('public')
                            ->required()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
