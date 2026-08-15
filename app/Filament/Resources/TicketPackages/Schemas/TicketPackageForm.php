<?php

namespace App\Filament\Resources\TicketPackages\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class TicketPackageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Ticket Package Configuration')
                    ->description('Define tier pricing, package name, and event-day validity rules.')
                    ->icon('heroicon-o-ticket')
                    ->schema([
                        Grid::make(3)->schema([
                            Select::make('tournament_id')
                                ->label('Tournament')
                                ->relationship('tournament', 'name')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->live()
                                ->columnSpan(1),

                            TextInput::make('name')
                                ->label('Package Name')
                                ->placeholder('e.g. VIP Season Pass, Day 1 General, Early Bird')
                                ->required()
                                ->maxLength(255)
                                ->columnSpan(1),

                            TextInput::make('price')
                                ->label('Ticket Price (NPR)')
                                ->numeric()
                                ->prefix('Rs.')
                                ->required()
                                ->default(200.00)
                                ->minValue(0)
                                ->columnSpan(1),

                            Select::make('validity_type')
                                ->label('Validity Type')
                                ->options([
                                    'all_days' => 'All Event Days (Full Tournament Access)',
                                    'specific_days' => 'Specific Selected Event Days',
                                    'single_day' => 'Single Day Pass',
                                ])
                                ->default('all_days')
                                ->required()
                                ->live()
                                ->columnSpan(1),

                            TextInput::make('order')
                                ->label('Display Order')
                                ->numeric()
                                ->default(1)
                                ->columnSpan(1),

                            Toggle::make('is_active')
                                ->label('Active & Available for Sale')
                                ->default(true)
                                ->columnSpan(1),

                            Select::make('eventDays')
                                ->label('Assigned / Default Event Days')
                                ->relationship('eventDays', 'day_name', function ($query, Get $get) {
                                    $tournamentId = $get('tournament_id');
                                    if ($tournamentId) {
                                        $query->where('tournament_id', $tournamentId);
                                    }
                                })
                                ->multiple()
                                ->preload()
                                ->visible(fn (Get $get): bool => $get('validity_type') === 'specific_days' || $get('validity_type') === 'single_day')
                                ->helperText('Select which tournament event days this package grants access to.')
                                ->columnSpanFull(),

                            Textarea::make('description')
                                ->label('Package Perks & Description')
                                ->placeholder('Includes front-row seating, exclusive jersey, backstage access...')
                                ->rows(3)
                                ->columnSpanFull(),
                        ]),
                    ]),
            ]);
    }
}
