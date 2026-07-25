<?php

namespace App\Filament\Mukhyadwar\Widgets;

use App\Models\Team;
use App\Models\TournamentRegistration;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class ParticipantStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $participantId = Auth::id();
        $myTeamsCount = Team::where('manager_id', $participantId)->count();
        $myApplicationsCount = TournamentRegistration::where('registered_by', $participantId)->count();
        $approvedCount = TournamentRegistration::where('registered_by', $participantId)->where('status', 'approved')->count();

        return [
            Stat::make('My Esports Squads', $myTeamsCount)
                ->description('Registered teams')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),

            Stat::make('Tournament Applications', $myApplicationsCount)
                ->description('Submitted tournament entries')
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->color('info'),

            Stat::make('Approved & Verified', $approvedCount)
                ->description('Active tournament entries')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
        ];
    }
}
