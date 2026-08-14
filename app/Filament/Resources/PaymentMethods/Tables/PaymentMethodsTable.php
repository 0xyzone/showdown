<?php

namespace App\Filament\Resources\PaymentMethods\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentMethodsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order')
                    ->label('#')
                    ->sortable()
                    ->width('60px'),

                TextColumn::make('name')
                    ->label('Payment Method')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('code')
                    ->label('Code')
                    ->fontFamily('mono')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('account_details')
                    ->label('Instructions / Details')
                    ->limit(40)
                    ->placeholder('—'),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('tournaments_count')
                    ->label('Tournaments')
                    ->counts('tournaments')
                    ->badge()
                    ->color('info'),
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
