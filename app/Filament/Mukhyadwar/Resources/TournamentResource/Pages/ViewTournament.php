<?php

namespace App\Filament\Mukhyadwar\Resources\TournamentResource\Pages;

use App\Filament\Mukhyadwar\Resources\TournamentResource;
use App\Models\Tournament;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewTournament extends ViewRecord
{
    protected static string $resource = TournamentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('registerTeamHeader')
                ->label('Register Team Now')
                ->icon('heroicon-o-sparkles')
                ->color('success')
                ->visible(fn (Tournament $record): bool => in_array($record->status, ['registration_open', 'ongoing']) || $record->is_active)
                ->form(fn (Tournament $record) => TournamentResource::getRegistrationFormSchema($record))
                ->action(fn (array $data, Tournament $record) => TournamentResource::processTeamRegistration($data, $record)),
        ];
    }
}
