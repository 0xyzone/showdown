<?php

namespace App\Filament\Resources\Tickets\Tables;

use App\Models\Ticket;
use App\Models\TicketPackage;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TicketsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                /** @var User|null $user */
                $user = Auth::user();
                if ($user && ! $user->hasRole('super_admin')) {
                    $query->whereHas('ticketPurchase', function ($q) use ($user) {
                        $q->where('seller_id', $user->id)
                            ->orWhere('created_by', $user->id);
                    });
                }
            })
            ->columns([
                TextColumn::make('ticket_number')
                    ->label('Ticket #')
                    ->searchable()
                    ->sortable()
                    ->fontFamily('mono')
                    ->weight('bold')
                    ->copyable(),

                TextColumn::make('tournament.name')
                    ->label('Tournament')
                    ->searchable()
                    ->sortable()
                    ->limit(20),

                TextColumn::make('package_name')
                    ->label('Package')
                    ->badge()
                    ->color('info')
                    ->placeholder('Standard'),

                TextColumn::make('customer_name')
                    ->label('Attendee')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('customer_phone')
                    ->label('Phone')
                    ->searchable(),

                TextColumn::make('validEventDays_summary')
                    ->label('Valid Days')
                    ->state(function (Ticket $record): string {
                        if ($record->validEventDays->isEmpty()) {
                            return 'All Days';
                        }

                        return $record->validEventDays->pluck('day_name')->join(', ');
                    })
                    ->limit(25)
                    ->badge()
                    ->color('gray'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'valid' => 'success',
                        'used' => 'warning',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('attendances_count')
                    ->label('Check-Ins')
                    ->counts('attendances')
                    ->badge()
                    ->color('primary'),

                TextColumn::make('ticketPurchase.order_number')
                    ->label('Order Ref')
                    ->fontFamily('mono')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Issued At')
                    ->dateTime('M d, Y • h:i A')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('tournament_id')
                    ->label('Tournament')
                    ->relationship('tournament', 'name'),

                SelectFilter::make('ticket_package_id')
                    ->label('Ticket Package')
                    ->options(TicketPackage::pluck('name', 'id')),

                SelectFilter::make('status')
                    ->label('Ticket Status')
                    ->options([
                        'valid' => 'Valid',
                        'used' => 'Used',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->actions([
                Action::make('verify_gate')
                    ->label('Gate Verification')
                    ->icon('heroicon-o-qr-code')
                    ->color('primary')
                    ->url(fn (Ticket $record): string => route('ticket.verify', ['token' => $record->verification_token]))
                    ->openUrlInNewTab(),

                ViewAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
