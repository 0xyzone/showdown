<?php

namespace App\Filament\Mukhyadwar\Resources\Pages;

use App\Filament\Mukhyadwar\Resources\RegistrationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRegistrations extends ListRecords
{
    protected static string $resource = RegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Register Team for Tournament'),
        ];
    }
}
