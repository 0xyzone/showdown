<?php

namespace App\Filament\Resources\Incomes\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class IncomeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Income Details')
                    ->description('Record new incoming revenue or fund contributions.')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('amount')
                                ->numeric()
                                ->prefix('Rs.')
                                ->required()
                                ->minValue(0.01)
                                ->columnSpan(1),

                            DatePicker::make('income_date')
                                ->label('Income Date')
                                ->default(now())
                                ->required()
                                ->columnSpan(1),

                            Select::make('income_type')
                                ->label('Income Type')
                                ->options([
                                    'sponsorship' => 'Sponsorship',
                                    'donation' => 'Donation',
                                    'self_contribution' => 'Self Contribution',
                                ])
                                ->required()
                                ->native(false)
                                ->columnSpan(1),

                            TextInput::make('received_from')
                                ->label('Received From')
                                ->placeholder('Organization / Person Name')
                                ->required()
                                ->maxLength(255)
                                ->columnSpan(1),

                            TextInput::make('received_by')
                                ->label('Received By')
                                ->placeholder('Person or Account handling receipt')
                                ->required()
                                ->maxLength(255)
                                ->columnSpan(1),

                            Select::make('entered_by')
                                ->label('Entered By')
                                ->relationship('enteredBy', 'name')
                                ->default(fn () => auth()->id())
                                ->disabled()
                                ->dehydrated()
                                ->required()
                                ->columnSpan(1),
                        ]),

                        Textarea::make('notes')
                            ->label('Additional Notes')
                            ->rows(3)
                            ->columnSpanFull(),

                        Hidden::make('entered_by')
                            ->default(fn () => auth()->id()),
                    ]),
            ]);
    }
}
