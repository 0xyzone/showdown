<?php

namespace App\Filament\Widgets;

use App\Models\SponsorQuery;
use App\Models\Tournament;
use App\Models\TournamentRegistration;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TournamentStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $activeTournament = Tournament::where('is_active', true)->first();
        $pendingRegistrations = TournamentRegistration::where('status', 'pending')->count();
        $pendingQueries = SponsorQuery::where('status', 'pending')->count();
        $totalPrizePool = Tournament::sum('prize_pool_total');

        return [
            Stat::make('Featured Active Event', $activeTournament?->name ?? 'None Active')
                ->description('Current homepage featured tournament')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('success'),

            Stat::make('Pending Registrations', $pendingRegistrations)
                ->description('Team Applications awaiting verification')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingRegistrations > 0 ? 'warning' : 'secondary'),

            Stat::make('Pending Sponsor Queries', $pendingQueries)
                ->description('Brand partnership inquiries')
                ->descriptionIcon('heroicon-m-envelope')
                ->color($pendingQueries > 0 ? 'info' : 'secondary'),

            Stat::make('Total Prize Pool', 'Rs. '.number_format($totalPrizePool))
                ->description('Combined tournament prize money')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('primary'),
        ];
    }
}
