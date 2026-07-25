<?php

namespace App\Filament\Mukhyadwar\Resources\RegistrationResource\Pages;

use App\Filament\Mukhyadwar\Resources\RegistrationResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateRegistration extends CreateRecord
{
    protected static string $resource = RegistrationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['registered_by'] = Auth::id();
        $data['status'] = 'pending';

        return $data;
    }
}
