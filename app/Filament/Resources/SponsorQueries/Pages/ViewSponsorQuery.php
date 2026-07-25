<?php

namespace App\Filament\Resources\SponsorQueries\Pages;

use App\Filament\Resources\SponsorQueries\SponsorQueryResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSponsorQuery extends ViewRecord
{
    protected static string $resource = SponsorQueryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
