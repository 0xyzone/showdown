<?php

namespace App\Filament\Widgets;

use App\Models\TournamentRegistration;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestRegistrationsWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Recent Team Registrations';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                TournamentRegistration::query()->latest()->take(5)
            )
            ->columns([
                TextColumn::make('tournament.name')
                    ->label('Tournament')
                    ->weight('bold'),
                TextColumn::make('team.name')
                    ->label('Team Name'),
                TextColumn::make('registeredBy.name')
                    ->label('Manager'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'disqualified' => 'danger',
                        default => 'secondary',
                    }),
                TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime('M d, H:i'),
            ]);
    }
}
