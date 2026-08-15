<?php

namespace App\Filament\Resources\TicketPackages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TicketPackagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order')
                    ->label('#')
                    ->sortable()
                    ->width('60px'),

                TextColumn::make('tournament.name')
                    ->label('Tournament')
                    ->searchable()
                    ->sortable()
                    ->limit(25),

                TextColumn::make('name')
                    ->label('Package Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('price')
                    ->label('Price')
                    ->money('NPR')
                    ->sortable()
                    ->color('success'),

                TextColumn::make('validity_type')
                    ->label('Validity Scope')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'all_days' => 'success',
                        'specific_days' => 'info',
                        'single_day' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'all_days' => 'All Days',
                        'specific_days' => 'Specific Days',
                        'single_day' => 'Single Day',
                        default => $state,
                    }),

                TextColumn::make('purchases_count')
                    ->label('Sold')
                    ->counts('purchases')
                    ->badge()
                    ->color('primary'),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('tournament_id')
                    ->label('Tournament')
                    ->relationship('tournament', 'name'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('order', 'asc');
    }
}
