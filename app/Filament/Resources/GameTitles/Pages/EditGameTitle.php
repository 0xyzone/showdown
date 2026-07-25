<?php

namespace App\Filament\Resources\GameTitles\Pages;

use App\Filament\Resources\GameTitles\GameTitleResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditGameTitle extends EditRecord
{
    protected static string $resource = GameTitleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
