<?php

namespace App\Exports;

use App\Models\StaffAttendance;
use Carbon\Carbon;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StaffAttendanceReportExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    public function __construct(
        protected array $filters = []
    ) {}

    public function collection(): Enumerable
    {
        $user = auth()->user();
        $query = StaffAttendance::with(['user', 'correctedByAdmin'])
            ->orderBy('date', 'desc');

        if ($user && ! $user->hasRole('super_admin') && ! $user->hasRole('attendance_manager') && ! $user->can('ViewAny:StaffAttendance')) {
            $query->where('user_id', $user->id);
        } elseif (! empty($this->filters['user_id'])) {
            $query->where('user_id', $this->filters['user_id']);
        }

        if (! empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (! empty($this->filters['location_mode'])) {
            $query->where('location_mode', $this->filters['location_mode']);
        }

        if (! empty($this->filters['date_from'])) {
            $query->whereDate('date', '>=', Carbon::parse($this->filters['date_from'])->toDateString());
        }

        if (! empty($this->filters['date_to'])) {
            $query->whereDate('date', '<=', Carbon::parse($this->filters['date_to'])->toDateString());
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Staff Member',
            'Email',
            'Date',
            'Punch In Time',
            'Punch Out Time',
            'Worked Time',
            'Worked Minutes',
            'Status',
            'Location Mode',
            'Punch-In Distance (m)',
            'Biometric Verified',
            'Manually Adjusted',
            'Adjusted By',
            'Adjustment Reason',
        ];
    }

    /**
     * @param  StaffAttendance  $row
     */
    public function map(mixed $row): array
    {
        /** @var StaffAttendance $row */
        return [
            $row->user?->name ?? 'N/A',
            $row->user?->email ?? 'N/A',
            $row->date ? $row->date->format('Y-m-d') : 'N/A',
            $row->punch_in_at ? $row->punch_in_at->format('H:i:s') : '—',
            $row->punch_out_at ? $row->punch_out_at->format('H:i:s') : '—',
            $row->formatted_worked_time,
            $row->worked_minutes,
            strtoupper($row->status),
            strtoupper($row->location_mode),
            $row->punch_in_distance_meters ?? '—',
            $row->punch_in_verified_biometric ? 'YES' : 'NO',
            $row->is_manually_corrected ? 'YES' : 'NO',
            $row->correctedByAdmin?->name ?? '—',
            $row->correction_reason ?? '—',
        ];
    }

    public function styles(Worksheet $sheet): ?array
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FFFFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF0F172A'], // Dark slate
                ],
            ],
        ];
    }
}
