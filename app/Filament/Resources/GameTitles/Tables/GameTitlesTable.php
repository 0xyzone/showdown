<?php

namespace App\Filament\Resources\GameTitles\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GameTitlesTable
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
                    ->label('Game Title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('developer')
                    ->label('Developer')
                    ->searchable(),
                TextColumn::make('game_type')
                    ->label('Genre')
                    ->badge()
                    ->color('primary')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
