<?php

namespace App\Filament\Resources\Tournaments\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TournamentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo_path')
                    ->label('Logo')
                    ->disk('public')
                    ->defaultImageUrl(asset('images/sponsor_placeholder.png'))
                    ->square(),
                TextColumn::make('name')
                    ->label('Tournament')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('gameTitles.name')
                    ->label('Game Title')
                    ->listWithLineBreaks()
                    ->badge()
                    ->limitList(3)
                    ->expandableLimitedList()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'secondary',
                        'registration_open' => 'success',
                        'ongoing' => 'info',
                        'completed' => 'warning',
                        'cancelled' => 'danger',
                        default => 'secondary',
                    })
                    ->sortable(),
                ToggleColumn::make('is_active')
                    ->label('Active Event')
                    ->sortable(),
                TextColumn::make('prize_pool_total')
                    ->label('Prize Pool')
                    ->money('NPR')
                    ->sortable(),
                TextColumn::make('start_date')
                    ->label('Starts')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'registration_open' => 'Registration Open',
                        'ongoing' => 'Ongoing',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
