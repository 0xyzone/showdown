<?php

namespace App\Filament\Resources\Expenses\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ExpenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Expense Details')
                    ->description('Log new organizational expenditure with receipt documentation.')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('amount')
                                ->numeric()
                                ->prefix('Rs.')
                                ->required()
                                ->minValue(0.01)
                                ->columnSpan(1),

                            DatePicker::make('expense_date')
                                ->label('Expense Date')
                                ->default(now())
                                ->required()
                                ->columnSpan(1),

                            Select::make('expense_type_id')
                                ->label('Expense Type')
                                ->relationship('expenseType', 'name')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->createOptionForm([
                                    TextInput::make('name')
                                        ->required()
                                        ->maxLength(255)
                                        ->unique('expense_types', 'name'),
                                    Textarea::make('description')
                                        ->rows(2),
                                ])
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

                        FileUpload::make('receipt_images')
                            ->label('Receipt Images')
                            ->multiple()
                            ->image()
                            ->disk('public')
                            ->directory('expense-receipts')
                            ->maxFiles(5)
                            ->columnSpanFull(),

                        Textarea::make('notes')
                            ->label('Additional Notes / Remarks')
                            ->rows(3)
                            ->columnSpanFull(),

                        Hidden::make('entered_by')
                            ->default(fn () => auth()->id()),
                    ]),
            ]);
    }
}
