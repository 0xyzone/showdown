<?php

namespace App\Filament\Resources\TicketPackages;

use App\Filament\Resources\TicketPackages\Pages\CreateTicketPackage;
use App\Filament\Resources\TicketPackages\Pages\EditTicketPackage;
use App\Filament\Resources\TicketPackages\Pages\ListTicketPackages;
use App\Filament\Resources\TicketPackages\Schemas\TicketPackageForm;
use App\Filament\Resources\TicketPackages\Tables\TicketPackagesTable;
use App\Models\TicketPackage;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class TicketPackageResource extends Resource
{
    protected static ?string $model = TicketPackage::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-gift';

    protected static ?string $navigationLabel = 'Ticket Packages';

    protected static string|\UnitEnum|null $navigationGroup = 'Tournament Management';

    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return TicketPackageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TicketPackagesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTicketPackages::route('/'),
            'create' => CreateTicketPackage::route('/create'),
            'edit' => EditTicketPackage::route('/{record}/edit'),
        ];
    }
}
