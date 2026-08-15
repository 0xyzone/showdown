<?php

namespace App\Filament\Resources\StaffAttendanceProfiles\Pages;

use App\Filament\Resources\StaffAttendanceProfiles\StaffAttendanceProfileResource;
use Filament\Resources\Pages\EditRecord;

class EditStaffAttendanceProfile extends EditRecord
{
    protected static string $resource = StaffAttendanceProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
