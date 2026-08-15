<?php

namespace App\Filament\Widgets;

use App\Models\TicketPurchase;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentTicketSalesWidget extends BaseWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Recent Ticket Sales & Gate Passes';

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                $user = auth()->user();
                $query = TicketPurchase::query()->with(['tournament', 'seller', 'ticketPackage'])->latest();

                if ($user && ! $user->hasRole('super_admin') && ! $user->can('ViewAny:TicketPurchase')) {
                    $query->where('seller_id', $user->id);
                }

                return $query;
            })
            ->columns([
                TextColumn::make('order_number')
                    ->label('Order #')
                    ->fontFamily('mono')
                    ->weight('bold'),

                TextColumn::make('tournament.name')
                    ->label('Tournament')
                    ->limit(20),

                TextColumn::make('package_name')
                    ->label('Package')
                    ->badge()
                    ->color('info')
                    ->placeholder('Standard'),

                TextColumn::make('customer_name')
                    ->label('Customer'),

                TextColumn::make('quantity')
                    ->label('Qty')
                    ->alignCenter(),

                TextColumn::make('total_amount')
                    ->label('Amount')
                    ->money('NPR')
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

                TextColumn::make('seller.name')
                    ->label('Staff Seller')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('created_at')
                    ->label('Date & Time')
                    ->dateTime('M d, h:i A'),
            ])
            ->actions([
                Action::make('download_pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn (TicketPurchase $record): string => route('admin.ticket-purchases.pdf', $record))
                    ->openUrlInNewTab()
                    ->visible(fn (TicketPurchase $record): bool => $record->payment_status === 'paid'),
            ])
            ->paginated([5, 10]);
    }
}
