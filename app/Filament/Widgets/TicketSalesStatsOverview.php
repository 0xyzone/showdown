<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use App\Models\TicketPurchase;
use App\Models\User;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class TicketSalesStatsOverview extends BaseWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 12;

    protected function getStats(): array
    {
        /** @var User|null $user */
        $user = Auth::user();
        $isSuperAdmin = (bool) $user?->hasRole('super_admin');

        if (! $isSuperAdmin) {
            // Staff-specific scoped metrics
            $mySales = TicketPurchase::where('seller_id', $user?->id)->where('payment_status', 'paid');
            $myTicketsSold = (clone $mySales)->sum('quantity');
            $myRevenue = (clone $mySales)->sum('total_amount');
            $myOrdersCount = TicketPurchase::where('seller_id', $user?->id)->count();
            $mySalesToday = (clone $mySales)->whereDate('created_at', Carbon::today())->sum('quantity');

            return [
                Stat::make('My Tickets Sold', number_format($myTicketsSold))
                    ->description('Total tickets issued by you')
                    ->descriptionIcon('heroicon-m-ticket')
                    ->color('success'),

                Stat::make('My Total Sales', 'Rs. '.number_format($myRevenue, 2))
                    ->description('Total collection credited to your staff ID')
                    ->descriptionIcon('heroicon-m-banknotes')
                    ->color('primary'),

                Stat::make('My Sales Today', number_format($mySalesToday))
                    ->description('Tickets sold today')
                    ->descriptionIcon('heroicon-m-calendar')
                    ->color('info'),

                Stat::make('My Purchase Transactions', number_format($myOrdersCount))
                    ->description('Total counter orders handled')
                    ->descriptionIcon('heroicon-m-shopping-bag')
                    ->color('secondary'),
            ];
        }

        // Super Admin Global Metrics
        $paidPurchases = TicketPurchase::where('payment_status', 'paid');
        $totalTicketsSold = (clone $paidPurchases)->sum('quantity');
        $totalRevenue = (clone $paidPurchases)->sum('total_amount');
        $ticketsSoldToday = (clone $paidPurchases)->whereDate('created_at', Carbon::today())->sum('quantity');
        $totalCheckedIn = Ticket::where('is_used', true)->count();
        $totalIssued = Ticket::count();
        $remainingUnused = max(0, $totalIssued - $totalCheckedIn);

        return [
            Stat::make('Total Tickets Sold', number_format($totalTicketsSold))
                ->description('All paid tournament tickets')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('success'),

            Stat::make('Total Ticket Revenue', 'Rs. '.number_format($totalRevenue, 2))
                ->description('Gross ticket admission collection')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('primary'),

            Stat::make('Tickets Sold Today', number_format($ticketsSoldToday))
                ->description('Admissions sold across all counters today')
                ->descriptionIcon('heroicon-m-bolt')
                ->color('info'),

            Stat::make('Gate Checked-In', number_format($totalCheckedIn))
                ->description("{$totalCheckedIn} attended &bull; {$remainingUnused} unused")
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('warning'),
        ];
    }
}
