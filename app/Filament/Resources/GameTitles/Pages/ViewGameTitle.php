<?php

namespace App\Filament\Resources\GameTitles\Pages;

use App\Filament\Resources\GameTitles\GameTitleResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewGameTitle extends ViewRecord
{
    protected static string $resource = GameTitleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
