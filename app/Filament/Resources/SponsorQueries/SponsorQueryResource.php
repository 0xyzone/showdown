<?php

namespace App\Filament\Resources\SponsorQueries;

use App\Filament\Resources\SponsorQueries\Pages\CreateSponsorQuery;
use App\Filament\Resources\SponsorQueries\Pages\EditSponsorQuery;
use App\Filament\Resources\SponsorQueries\Pages\ListSponsorQueries;
use App\Filament\Resources\SponsorQueries\Pages\ViewSponsorQuery;
use App\Filament\Resources\SponsorQueries\Schemas\SponsorQueryForm;
use App\Filament\Resources\SponsorQueries\Schemas\SponsorQueryInfolist;
use App\Filament\Resources\SponsorQueries\Tables\SponsorQueriesTable;
use App\Models\SponsorQuery;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class SponsorQueryResource extends Resource
{
    protected static ?string $model = SponsorQuery::class;

    protected static ?string $navigationLabel = 'Sponsor Queries';

    protected static ?string $modelLabel = 'Sponsor Query';

    protected static ?string $pluralModelLabel = 'Sponsor Queries';

    protected static string|UnitEnum|null $navigationGroup = 'Partnerships & Sponsorships';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-inbox-arrow-down';

    public static function form(Schema $schema): Schema
    {
        return SponsorQueryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SponsorQueryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SponsorQueriesTable::configure($table);
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
            'index' => ListSponsorQueries::route('/'),
            'create' => CreateSponsorQuery::route('/create'),
            'view' => ViewSponsorQuery::route('/{record}'),
            'edit' => EditSponsorQuery::route('/{record}/edit'),
        ];
    }
}
