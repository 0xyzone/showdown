<?php

namespace App\Filament\Resources\StaffAttendances\Pages;

use App\Filament\Resources\StaffAttendances\StaffAttendanceResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListStaffAttendances extends ListRecords
{
    protected static string $resource = StaffAttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('open_terminal')
                ->label('Open Attendance Terminal')
                ->icon('heroicon-o-finger-print')
                ->color('primary')
                ->url(route('attendance.index'))
                ->openUrlInNewTab(),
        ];
    }
}
