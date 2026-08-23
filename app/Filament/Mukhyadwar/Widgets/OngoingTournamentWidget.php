<?php

namespace App\Filament\Mukhyadwar\Widgets;

use App\Filament\Mukhyadwar\Resources\TournamentResource;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\TournamentRegistration;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class OngoingTournamentWidget extends Widget implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected string $view = 'filament.mukhyadwar.widgets.ongoing-tournament-widget';

    protected int|string|array $columnSpan = 'full';

    public ?Tournament $tournament = null;

    public bool $isRegistered = false;

    public function mount(): void
    {
        $this->tournament = Tournament::where('status', '!=', 'draft')
            ->where(function ($query) {
                $query->where('is_active', true)
                    ->orWhere('status', 'registration_open');
            })
            ->first();

        if ($this->tournament) {
            $userTeamIds = Team::where('manager_id', Auth::id())->pluck('id');
            $this->isRegistered = TournamentRegistration::where('tournament_id', $this->tournament->id)
                ->whereIn('team_id', $userTeamIds)
                ->exists();
        }
    }

    public function registerAction(): Action
    {
        return Action::make('registerAction')
            ->label('Register Team Now')
            ->icon('heroicon-o-sparkles')
            ->color('success')
            ->form(fn () => $this->tournament ? TournamentResource::getRegistrationFormSchema($this->tournament) : [])
            ->action(function (array $data) {
                if (! $this->tournament) {
                    return;
                }
                TournamentResource::processTeamRegistration($data, $this->tournament);
                $this->mount();
            });
    }
}
