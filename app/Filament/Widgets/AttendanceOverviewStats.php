<?php

namespace App\Filament\Widgets;

use App\Models\StaffAttendance;
use App\Models\User;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AttendanceOverviewStats extends BaseWidget
{
    use HasWidgetShield;

    protected static ?int $sort = -20;

    protected function getStats(): array
    {
        $user = auth()->user();
        $isManager = $user?->hasRole('super_admin') || $user?->hasRole('attendance_manager');

        if (! $isManager) {
            // Staff-specific KPIs
            $today = StaffAttendance::where('user_id', $user?->id)
                ->whereDate('date', Carbon::today())
                ->first();

            $weekStart = Carbon::now()->startOfWeek();
            $weekMinutes = StaffAttendance::where('user_id', $user?->id)
                ->whereDate('date', '>=', $weekStart)
                ->sum('worked_minutes');
            $weekHours = round($weekMinutes / 60, 1);

            $statusText = 'Not Clocked In';
            $statusColor = 'secondary';
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
                Stat::make("Today's Attendance Status", $statusText)
                    ->description($today?->punch_in_at ? 'Clocked in at '.$today->punch_in_at->format('h:i A') : 'No clock-in recorded')
                    ->descriptionIcon('heroicon-m-clock')
                    ->color($statusColor),

                Stat::make("Today's Worked Time", $today ? $today->formatted_worked_time : '0h 0m')
                    ->description($today?->location_mode ? ucfirst($today->location_mode).' mode' : '—')
                    ->descriptionIcon('heroicon-m-briefcase')
                    ->color('primary'),

                Stat::make('Total Hours This Week', "{$weekHours} hrs")
                    ->description('Since '.$weekStart->format('M d'))
                    ->descriptionIcon('heroicon-m-calendar')
                    ->color('teal'),
            ];
        }

        // Global Admin / Manager KPIs
        $totalStaff = User::count();
        $todayRecords = StaffAttendance::whereDate('date', Carbon::today())->get();

        $clockedInToday = $todayRecords->count();
        $currentlyWorking = $todayRecords->whereNotNull('punch_in_at')->whereNull('punch_out_at')->count();
        $completedToday = $todayRecords->whereNotNull('punch_out_at')->count();
        $remoteToday = $todayRecords->where('location_mode', 'remote')->count();

        return [
            Stat::make('Staff Clocked In Today', "{$clockedInToday} / {$totalStaff}")
                ->description("{$currentlyWorking} currently active working")
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),

            Stat::make('Currently Working', number_format($currentlyWorking))
                ->description('Active open sessions')
                ->descriptionIcon('heroicon-m-play')
                ->color('teal'),

            Stat::make('Completed Shifts Today', number_format($completedToday))
                ->description('Clocked out successfully')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('info'),

            Stat::make('Remote Staff Today', number_format($remoteToday))
                ->description('Location-exempt attendance')
                ->descriptionIcon('heroicon-m-home')
                ->color('primary'),
        ];
    }
}
