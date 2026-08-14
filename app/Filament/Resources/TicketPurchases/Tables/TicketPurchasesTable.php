<?php

namespace App\Filament\Resources\TicketPurchases\Tables;

use App\Models\PaymentMethod;
use App\Models\TicketPurchase;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TicketPurchasesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->label('Order #')
                    ->searchable()
                    ->sortable()
                    ->fontFamily('mono')
                    ->weight('bold')
                    ->copyable(),

                TextColumn::make('tournament.name')
                    ->label('Tournament')
                    ->searchable()
                    ->sortable()
                    ->limit(25),

                TextColumn::make('customer_name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('customer_phone')
                    ->label('Phone')
                    ->searchable(),

                TextColumn::make('quantity')
                    ->label('Tickets')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('total_amount')
                    ->label('Total Paid')
                    ->money('NPR')
                    ->sortable()
                    ->color('success'),

                TextColumn::make('payment_status')
                    ->label('Payment')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'unpaid' => 'warning',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('payment_source')
                    ->label('Source')
                    ->badge()
                    ->color('info'),

                TextColumn::make('tickets_summary')
                    ->label('Attendance')
                    ->state(function (TicketPurchase $record): string {
                        $total = $record->tickets()->count();
                        if ($total === 0) {
                            return '0 issued';
                        }
                        $used = $record->tickets()->where('is_used', true)->count();

                        return "{$used}/{$total} used";
                    })
                    ->badge()
                    ->color(fn (string $state): string => str_contains($state, '0/') ? 'gray' : 'primary'),

                TextColumn::make('createdBy.name')
                    ->label('Admin Cashier')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('M d, Y • h:i A')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('tournament_id')
                    ->label('Tournament')
                    ->relationship('tournament', 'name'),

                SelectFilter::make('payment_status')
                    ->label('Payment Status')
                    ->options([
                        'paid' => 'Paid',
                        'unpaid' => 'Unpaid',
                        'cancelled' => 'Cancelled',
                    ]),

                SelectFilter::make('payment_method_id')
                    ->label('Payment Method')
                    ->options(PaymentMethod::pluck('name', 'id')),
            ])
            ->actions([
                Action::make('download_pdf')
                    ->label('PDF Tickets')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn (TicketPurchase $record): string => route('admin.ticket-purchases.pdf', $record))
                    ->openUrlInNewTab()
                    ->visible(fn (TicketPurchase $record): bool => $record->payment_status === 'paid'),

                Action::make('view_receipt')
                    ->label('Receipt')
                    ->icon('heroicon-o-document-text')
                    ->color('gray')
                    ->url(fn (TicketPurchase $record): string => route('admin.ticket-purchases.receipt', $record))
                    ->openUrlInNewTab()
                    ->visible(fn (TicketPurchase $record): bool => ! empty($record->payment_receipt_path)),

                ViewAction::make(),
                EditAction::make(),
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
