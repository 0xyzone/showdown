<?php

namespace App\Filament\Widgets;

use App\Models\StaffAttendance;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MyAttendanceStatsWidget extends BaseWidget
{
    use HasWidgetShield;

    protected static ?int $sort = -20;

    protected function getStats(): array
    {
        $user = auth()->user();
        if (! $user) {
            return [];
        }

        $today = StaffAttendance::where('user_id', $user->id)
            ->whereDate('date', Carbon::today())
            ->first();

        $weekStart = Carbon::now()->startOfWeek();
        $weekMinutes = StaffAttendance::where('user_id', $user->id)
            ->whereDate('date', '>=', $weekStart)
            ->sum('worked_minutes');
        $weekHours = round($weekMinutes / 60, 1);

        $statusText = 'Not Clocked In';
        $statusColor = 'gray';
        if ($today) {
            if ($today->punch_in_at && ! $today->punch_out_at) {
                $statusText = 'Currently Working';
                $statusColor = 'success';
            } elseif ($today->punch_out_at) {
                $statusText = 'Completed for Today';
                $statusColor = 'info';
            }
        }

        return [
            Stat::make("My Today's Status", $statusText)
                ->description($today?->punch_in_at ? 'Started at '.$today->punch_in_at->format('h:i A') : 'Punch in via attendance terminal')
                ->descriptionIcon('heroicon-m-clock')
                ->color($statusColor),

            Stat::make('Hours Worked Today', $today ? $today->formatted_worked_time : '0h 0m')
                ->description($today?->location_mode ? ucfirst($today->location_mode).' attendance' : '—')
                ->descriptionIcon('heroicon-m-bolt')
                ->color('primary'),

            Stat::make('This Week Total', "{$weekHours} hrs")
                ->description('Monday to Today')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('teal'),
        ];
    }
}
