<?php

namespace App\Filament\Mukhyadwar\Resources\RegistrationResource\Pages;

use App\Filament\Mukhyadwar\Resources\RegistrationResource;
use Filament\Resources\Pages\ViewRecord;

class ViewRegistration extends ViewRecord
{
    protected static string $resource = RegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
