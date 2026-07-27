<?php

namespace App\Filament\Resources\GameTitles\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GameTitleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Game Profile Card')
                    ->icon('heroicon-o-puzzle-piece')
                    ->schema([
                        Grid::make(3)->schema([
                            ImageEntry::make('logo_path')
                                ->label('Logo Emblem')
                                ->disk('public')
                                ->height(140)
                                ->width(140)
                                ->extraImgAttributes(['class' => 'object-contain rounded-xl bg-slate-900 p-2 border border-slate-800'])
                                ->defaultImageUrl(asset('images/sponsor_placeholder.png')),
                            Grid::make(2)->schema([
                                TextEntry::make('name')
                                    ->label('Title Name')
                                    ->weight('black')
                                    ->size('lg'),
                                TextEntry::make('developer')
                                    ->label('Developer / Publisher')
                                    ->placeholder('N/A'),
                                TextEntry::make('game_type')
                                    ->label('Genre')
                                    ->badge()
                                    ->color('primary'),
                                TextEntry::make('created_at')
                                    ->label('Created On')
                                    ->dateTime(),
                            ])->columnSpan(2),
                        ]),
                    ]),

                Section::make('Hero Header Banner')
                    ->icon('heroicon-o-photo')
                    ->schema([
                        ImageEntry::make('banner_path')
                            ->label('Banner Graphic')
                            ->disk('public')
                            ->extraImgAttributes(['class' => 'w-full max-h-72 object-cover rounded-2xl aspect-[16/9]'])
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
