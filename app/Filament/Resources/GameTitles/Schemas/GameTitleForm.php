<?php

namespace App\Filament\Resources\GameTitles\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

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
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state ?? '')))
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

                Section::make('Squad Roster Requirements')
                    ->description('Configure required main players and max substitutes for this game title. (Every team also allows 1 Coach and 1 Manager).')
                    ->icon('heroicon-o-user-group')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('min_main_players')
                                ->label('Required Main Players')
                                ->helperText('e.g. 5 for MLBB/Valorant, 4 for PUBG, 1 for eFootball')
                                ->numeric()
                                ->default(5)
                                ->required(),
                            TextInput::make('max_substitutes')
                                ->label('Maximum Substitutes Allowed')
                                ->helperText('e.g. 2 substitute players allowed')
                                ->numeric()
                                ->default(2)
                                ->required(),
                        ]),
                    ]),

                Section::make('Brand Graphics & Banners')
                    ->description('Upload logo emblem and hero header banner graphics.')
                    ->icon('heroicon-o-photo')
                    ->schema([
                        Grid::make(2)->schema([
                            FileUpload::make('logo_path')
                                ->label('Logo Emblem (1:1 Ratio, PNG)')
                                ->image()
                                ->imageEditor()
                                ->imageEditorAspectRatios([
                                    '1:1',
                                ])
                                ->acceptedFileTypes(['image/png'])
                                ->imageCropAspectRatio('1:1')
                                ->disk('public')
                                ->directory('games')
                                ->visibility('public'),
                            FileUpload::make('banner_path')
                                ->label('Hero Header Banner (16:9 Ratio)')
                                ->image()
                                ->imageEditor()
                                ->imageEditorAspectRatios([
                                    '16:9',
                                ])
                                ->imageCropAspectRatio('16:9')
                                ->disk('public')
                                ->directory('games')
                                ->visibility('public'),
                        ]),
                    ]),
            ]);
    }
}
