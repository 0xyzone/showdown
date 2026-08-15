<?php

namespace App\Filament\Resources\Tickets;

use App\Filament\Resources\Tickets\Pages\ListTickets;
use App\Filament\Resources\Tickets\Pages\ViewTicket;
use App\Filament\Resources\Tickets\Tables\TicketsTable;
use App\Models\Ticket;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationLabel = 'Issued Tickets';

    protected static string|\UnitEnum|null $navigationGroup = 'Tournament Management';

    protected static ?int $navigationSort = 7;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Ticket Admission Details')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('ticket_number')
                                ->label('Ticket Number')
                                ->disabled(),
                            TextInput::make('verification_token')
                                ->label('Verification Token')
                                ->disabled(),
                            TextInput::make('package_name')
                                ->label('Package Tier')
                                ->disabled(),
                            TextInput::make('customer_name')
                                ->label('Attendee Name')
                                ->disabled(),
                            TextInput::make('customer_phone')
                                ->label('Customer Phone')
                                ->disabled(),
                            TextInput::make('price')
                                ->label('Price Paid')
                                ->prefix('Rs.')
                                ->disabled(),
                        ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return TicketsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTickets::route('/'),
            'view' => ViewTicket::route('/{record}'),
        ];
    }
}
