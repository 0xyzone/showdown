<?php

namespace App\Filament\Resources\TicketPurchases;

use App\Filament\Resources\TicketPurchases\Pages\CreateTicketPurchase;
use App\Filament\Resources\TicketPurchases\Pages\EditTicketPurchase;
use App\Filament\Resources\TicketPurchases\Pages\ListTicketPurchases;
use App\Filament\Resources\TicketPurchases\Pages\ViewTicketPurchase;
use App\Filament\Resources\TicketPurchases\RelationManagers\TicketsRelationManager;
use App\Filament\Resources\TicketPurchases\Schemas\TicketPurchaseForm;
use App\Filament\Resources\TicketPurchases\Tables\TicketPurchasesTable;
use App\Models\TicketPurchase;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class TicketPurchaseResource extends Resource
{
    protected static ?string $model = TicketPurchase::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationLabel = 'Ticket Sales';

    protected static string|\UnitEnum|null $navigationGroup = 'Tournament Management';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return TicketPurchaseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TicketPurchasesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            TicketsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTicketPurchases::route('/'),
            'create' => CreateTicketPurchase::route('/create'),
            'view' => ViewTicketPurchase::route('/{record}'),
            'edit' => EditTicketPurchase::route('/{record}/edit'),
        ];
    }
}
