<?php

namespace App\Filament\Mukhyadwar\Resources\Pages;

use App\Filament\Mukhyadwar\Resources\TeamResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTeam extends CreateRecord
{
    protected static string $resource = TeamResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['manager_id'] = auth('participant')->id();

        return $data;
    }
}
