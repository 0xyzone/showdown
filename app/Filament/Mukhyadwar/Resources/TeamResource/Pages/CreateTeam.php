<?php

namespace App\Filament\Mukhyadwar\Resources\TeamResource\Pages;

use App\Filament\Mukhyadwar\Resources\TeamResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateTeam extends CreateRecord
{
    protected static string $resource = TeamResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['manager_id'] = Auth::id();

        return $data;
    }
}
