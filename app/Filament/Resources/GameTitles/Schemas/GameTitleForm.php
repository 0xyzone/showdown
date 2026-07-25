<?php

namespace App\Filament\Resources\GameTitles\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GameTitleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Game Discipline Identity')
                    ->description('Game title name, developer studio, and competitive genre.')
                    ->icon('heroicon-o-puzzle-piece')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('name')
                                ->label('Title Name')
                                ->placeholder('e.g. PUBG Mobile, Valorant')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('slug')
                                ->label('Slug')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('developer')
                                ->label('Developer / Publisher')
                                ->placeholder('e.g. Riot Games, KRAFTON')
                                ->maxLength(255),
                            Select::make('game_type')
                                ->label('Competitive Genre')
                                ->options([
                                    '5v5_moba' => '⚔️ 5v5 MOBA',
                                    'battle_royale' => '🪂 Battle Royale',
                                    'fps' => '🎯 Tactical FPS',
                                    '1v1' => '⚽ 1v1 Sports / Fighting',
                                ])
                                ->required()
                                ->default('5v5_moba')
                                ->columnSpanFull(),
                        ]),
                    ]),

                Section::make('Brand Graphics & Banners')
                    ->description('Upload logo emblem and hero header banner graphics.')
                    ->icon('heroicon-o-photo')
                    ->schema([
                        Grid::make(2)->schema([
                            FileUpload::make('logo_path')
                                ->label('Logo Emblem')
                                ->image()
                                ->disk('public')
                                ->directory('games')
                                ->visibility('public'),
                            FileUpload::make('banner_path')
                                ->label('Hero Header Banner')
                                ->image()
                                ->disk('public')
                                ->directory('games')
                                ->visibility('public'),
                        ]),
                    ]),
            ]);
    }
}
