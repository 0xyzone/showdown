<?php

namespace App\Filament\Resources\SponsorQueries\Pages;

use App\Filament\Resources\SponsorQueries\SponsorQueryResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditSponsorQuery extends EditRecord
{
    protected static string $resource = SponsorQueryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
