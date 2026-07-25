<?php

namespace App\Filament\Resources\GameTitles\Pages;

use App\Filament\Resources\GameTitles\GameTitleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGameTitles extends ListRecords
{
    protected static string $resource = GameTitleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
