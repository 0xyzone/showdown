<?php

namespace App\Filament\Resources\GameTitles;

use App\Filament\Resources\GameTitles\Pages\CreateGameTitle;
use App\Filament\Resources\GameTitles\Pages\EditGameTitle;
use App\Filament\Resources\GameTitles\Pages\ListGameTitles;
use App\Filament\Resources\GameTitles\Pages\ViewGameTitle;
use App\Filament\Resources\GameTitles\Schemas\GameTitleForm;
use App\Filament\Resources\GameTitles\Schemas\GameTitleInfolist;
use App\Filament\Resources\GameTitles\Tables\GameTitlesTable;
use App\Models\GameTitle;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class GameTitleResource extends Resource
{
    protected static ?string $model = GameTitle::class;

    protected static ?string $navigationLabel = 'Game Titles';

    protected static string|UnitEnum|null $navigationGroup = 'Tournaments & Arenas';

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-puzzle-piece';

    public static function form(Schema $schema): Schema
    {
        return GameTitleForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return GameTitleInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GameTitlesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGameTitles::route('/'),
            'create' => CreateGameTitle::route('/create'),
            'view' => ViewGameTitle::route('/{record}'),
            'edit' => EditGameTitle::route('/{record}/edit'),
        ];
    }
}
