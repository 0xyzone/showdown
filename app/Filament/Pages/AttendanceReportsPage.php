<?php

namespace App\Filament\Pages;

use App\Exports\StaffAttendanceReportExport;
use App\Models\StaffAttendance;
use Carbon\Carbon;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceReportsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static ?string $navigationLabel = 'Attendance & Timesheet Reports';

    protected static string|\UnitEnum|null $navigationGroup = 'Staff & Attendance';

    protected static ?int $navigationSort = 5;

    protected string $view = 'filament.pages.attendance-reports-page';

    public ?int $user_id = null;

    public ?string $status = null;

    public ?string $location_mode = null;

    public ?string $date_from = null;

    public ?string $date_to = null;

    public function mount(): void
    {
        $this->date_from = Carbon::now()->startOfMonth()->toDateString();
        $this->date_to = Carbon::now()->toDateString();
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        return $user->hasRole('super_admin') || $user->hasRole('attendance_manager') || $user->can('View:AttendanceReport');
    }

    public function resetFilters(): void
    {
        $this->user_id = null;
        $this->status = null;
        $this->location_mode = null;
        $this->date_from = Carbon::now()->startOfMonth()->toDateString();
        $this->date_to = Carbon::now()->toDateString();
    }

    public function getFilteredAttendanceQuery()
    {
        $user = auth()->user();
        $query = StaffAttendance::with(['user', 'correctedByAdmin'])
            ->orderBy('date', 'desc');

        if ($user && ! $user->hasRole('super_admin') && ! $user->hasRole('attendance_manager') && ! $user->can('ViewAny:StaffAttendance')) {
            $query->where('user_id', $user->id);
        } elseif ($this->user_id) {
            $query->where('user_id', $this->user_id);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->location_mode) {
            $query->where('location_mode', $this->location_mode);
        }

        if ($this->date_from) {
            $query->whereDate('date', '>=', Carbon::parse($this->date_from)->toDateString());
        }

        if ($this->date_to) {
            $query->whereDate('date', '<=', Carbon::parse($this->date_to)->toDateString());
        }

        return $query;
    }

    public function getSummaryStats(): array
    {
        $query = $this->getFilteredAttendanceQuery();
        $records = $query->get();

        $totalRecords = $records->count();
        $totalMinutes = $records->sum('worked_minutes');
        $totalHours = round($totalMinutes / 60, 1);

        $completedCount = $records->where('status', 'completed')->count();
        $remoteCount = $records->where('location_mode', 'remote')->count();
        $activeCount = $records->where('status', 'working')->count();

        return [
            'total_days' => $totalRecords,
            'total_hours' => $totalHours,
            'completed_count' => $completedCount,
            'remote_count' => $remoteCount,
            'active_count' => $activeCount,
        ];
    }

    public function exportExcel()
    {
        $filters = [
            'user_id' => $this->user_id,
            'status' => $this->status,
            'location_mode' => $this->location_mode,
            'date_from' => $this->date_from,
            'date_to' => $this->date_to,
        ];

        $filename = 'staff-attendance-timesheet-'.now()->format('Y-m-d-His').'.xlsx';

        return Excel::download(new StaffAttendanceReportExport($filters), $filename);
    }
}
