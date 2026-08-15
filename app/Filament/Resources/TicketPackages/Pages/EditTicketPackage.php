<?php

namespace App\Filament\Resources\TicketPackages\Pages;

use App\Filament\Resources\TicketPackages\TicketPackageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTicketPackage extends EditRecord
{
    protected static string $resource = TicketPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
