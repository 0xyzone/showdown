<?php

namespace App\Filament\Resources\TicketPackages\Pages;

use App\Filament\Resources\TicketPackages\TicketPackageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTicketPackage extends CreateRecord
{
    protected static string $resource = TicketPackageResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
