<?php

namespace App\Filament\Mukhyadwar\Resources\TournamentResource\Pages;

use App\Filament\Mukhyadwar\Resources\TournamentResource;
use Filament\Resources\Pages\ListRecords;

class ListTournaments extends ListRecords
{
    protected static string $resource = TournamentResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
