<?php

namespace App\Filament\Resources\SponsorQueries\Pages;

use App\Filament\Resources\SponsorQueries\SponsorQueryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSponsorQueries extends ListRecords
{
    protected static string $resource = SponsorQueryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
