<?php

namespace App\Filament\Resources\TicketPackages\Pages;

use App\Filament\Resources\TicketPackages\TicketPackageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTicketPackages extends ListRecords
{
    protected static string $resource = TicketPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('New Ticket Package'),
        ];
    }
}
