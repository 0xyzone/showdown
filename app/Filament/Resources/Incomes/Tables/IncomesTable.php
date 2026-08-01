<?php

namespace App\Filament\Resources\Incomes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class IncomesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('income_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('amount')
                    ->label('Amount')
                    ->money('NPR')
                    ->sortable(),

                TextColumn::make('income_type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'sponsorship' => 'Sponsorship',
                        'donation' => 'Donation',
                        'self_contribution' => 'Self Contribution',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'sponsorship' => 'success',
                        'donation' => 'info',
                        'self_contribution' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('received_from')
                    ->label('Received From')
                    ->searchable(),

                TextColumn::make('received_by')
                    ->label('Received By')
                    ->searchable(),

                TextColumn::make('enteredBy.name')
                    ->label('Entered By')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('income_date', 'desc')
            ->filters([
                SelectFilter::make('income_type')
                    ->label('Income Type')
                    ->options([
                        'sponsorship' => 'Sponsorship',
                        'donation' => 'Donation',
                        'self_contribution' => 'Self Contribution',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
