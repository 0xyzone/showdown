<?php

namespace App\Filament\Resources\Partners\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PartnersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo_url')
                    ->label('Logo')
                    ->disk('public')
                    ->defaultImageUrl(asset('images/sponsor_placeholder.png'))
                    ->square(),
                TextColumn::make('name')
                    ->label('Partner Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('title')
                    ->label('Category Title')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('level')
                    ->label('Tier Level')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'major' => 'success',
                        'standard' => 'secondary',
                        default => 'secondary',
                    })
                    ->sortable(),
                TextColumn::make('website_url')
                    ->label('Website')
                    ->url(fn ($record) => $record->website_url, true)
                    ->toggleable(),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('order')
                    ->label('Sort Order')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('level')
                    ->options([
                        'major' => 'Major Partner',
                        'standard' => 'Standard Partner',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
