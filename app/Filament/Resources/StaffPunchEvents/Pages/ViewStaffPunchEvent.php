<?php

namespace App\Filament\Resources\StaffPunchEvents\Pages;

use App\Filament\Resources\StaffPunchEvents\StaffPunchEventResource;
use Filament\Resources\Pages\ViewRecord;

class ViewStaffPunchEvent extends ViewRecord
{
    protected static string $resource = StaffPunchEventResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
