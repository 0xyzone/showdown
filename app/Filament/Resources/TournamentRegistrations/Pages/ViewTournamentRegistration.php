<?php

namespace App\Filament\Resources\TournamentRegistrations\Pages;

use App\Filament\Resources\TournamentRegistrations\TournamentRegistrationResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTournamentRegistration extends ViewRecord
{
    protected static string $resource = TournamentRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
