<?php

namespace App\Filament\Resources\TicketPurchases\RelationManagers;

use App\Models\Ticket;
use Filament\Actions\Action;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TicketsRelationManager extends RelationManager
{
    protected static string $relationship = 'tickets';

    protected static ?string $title = 'Issued Admission Tickets';

    protected static string|\BackedEnum|null $icon = 'heroicon-o-ticket';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ticket_number')
                    ->label('Ticket #')
                    ->searchable()
                    ->fontFamily('mono')
                    ->weight('bold')
                    ->copyable(),

                TextColumn::make('customer_name')
                    ->label('Attendee')
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'valid' => 'success',
                        'used' => 'warning',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),

                IconColumn::make('is_used')
                    ->label('Attended')
                    ->boolean(),

                TextColumn::make('used_at')
                    ->label('Checked In At')
                    ->dateTime('M d, Y • h:i A')
                    ->placeholder('Not yet attended'),

                TextColumn::make('verifiedBy.name')
                    ->label('Gate Staff')
                    ->placeholder('—'),
            ])
            ->actions([
                Action::make('verify_page')
                    ->label('Verify / Gate Check')
                    ->icon('heroicon-o-qr-code')
                    ->color('primary')
                    ->url(fn (Ticket $record): string => route('ticket.verify', ['token' => $record->verification_token]))
                    ->openUrlInNewTab(),
            ]);
    }
}
