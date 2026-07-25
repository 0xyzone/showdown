<?php

namespace App\Filament\Resources\TournamentRegistrations;

use App\Filament\Resources\TournamentRegistrations\Pages\CreateTournamentRegistration;
use App\Filament\Resources\TournamentRegistrations\Pages\EditTournamentRegistration;
use App\Filament\Resources\TournamentRegistrations\Pages\ListTournamentRegistrations;
use App\Filament\Resources\TournamentRegistrations\Pages\ViewTournamentRegistration;
use App\Filament\Resources\TournamentRegistrations\Schemas\TournamentRegistrationForm;
use App\Filament\Resources\TournamentRegistrations\Schemas\TournamentRegistrationInfolist;
use App\Filament\Resources\TournamentRegistrations\Tables\TournamentRegistrationsTable;
use App\Models\TournamentRegistration;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class TournamentRegistrationResource extends Resource
{
    protected static ?string $model = TournamentRegistration::class;

    protected static ?string $navigationLabel = 'Registrations';

    protected static string|UnitEnum|null $navigationGroup = 'Tournaments & Arenas';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    public static function form(Schema $schema): Schema
    {
        return TournamentRegistrationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TournamentRegistrationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TournamentRegistrationsTable::configure($table);
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
            'index' => ListTournamentRegistrations::route('/'),
            'create' => CreateTournamentRegistration::route('/create'),
            'view' => ViewTournamentRegistration::route('/{record}'),
            'edit' => EditTournamentRegistration::route('/{record}/edit'),
        ];
    }
}
