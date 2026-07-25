<?php

namespace App\Filament\Resources\Tournaments;

use App\Filament\Resources\Tournaments\Pages\CreateTournament;
use App\Filament\Resources\Tournaments\Pages\EditTournament;
use App\Filament\Resources\Tournaments\Pages\ListTournaments;
use App\Filament\Resources\Tournaments\Pages\ViewTournament;
use App\Filament\Resources\Tournaments\Schemas\TournamentForm;
use App\Filament\Resources\Tournaments\Schemas\TournamentInfolist;
use App\Filament\Resources\Tournaments\Tables\TournamentsTable;
use App\Models\Tournament;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class TournamentResource extends Resource
{
    protected static ?string $model = Tournament::class;

    protected static ?string $navigationLabel = 'Tournaments';

    protected static string|UnitEnum|null $navigationGroup = 'Tournaments & Arenas';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-trophy';

    public static function form(Schema $schema): Schema
    {
        return TournamentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TournamentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TournamentsTable::configure($table);
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
            'index' => ListTournaments::route('/'),
            'create' => CreateTournament::route('/create'),
            'view' => ViewTournament::route('/{record}'),
            'edit' => EditTournament::route('/{record}/edit'),
        ];
    }
}
