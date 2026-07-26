<?php

namespace App\Filament\Resources\TournamentRegistrations\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TournamentRegistrationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Registration & Championship Summary')
                    ->icon('heroicon-o-trophy')
                    ->schema([
                        Grid::make(4)->schema([
                            TextEntry::make('tournament.name')
                                ->label('Tournament')
                                ->weight('black')
                                ->size('lg'),
                            TextEntry::make('tournament.formatted_entry_fee')
                                ->label('Entry Fee')
                                ->badge()
                                ->color('warning'),
                            TextEntry::make('status')
                                ->label('Status')
                                ->badge()
                                ->color(fn (string $state): string => match ($state) {
                                    'pending' => 'warning',
                                    'approved' => 'success',
                                    'rejected' => 'danger',
                                    'disqualified' => 'danger',
                                    default => 'secondary',
                                }),
                            TextEntry::make('created_at')
                                ->label('Submitted On')
                                ->dateTime(),
                        ]),
                    ]),

                Section::make('Esports Team & Game Discipline')
                    ->icon('heroicon-o-user-group')
                    ->schema([
                        Grid::make(4)->schema([
                            TextEntry::make('team.name')
                                ->label('Team Squad')
                                ->weight('bold'),
                            TextEntry::make('team.tag')
                                ->label('Team Tag')
                                ->badge(),
                            TextEntry::make('team.gameTitle.name')
                                ->label('Game Title Discipline')
                                ->badge()
                                ->color('info'),
                            TextEntry::make('registeredBy.name')
                                ->label('Manager / Contact Person'),
                            TextEntry::make('registeredBy.email')
                                ->label('Manager Email')
                                ->copyable(),
                            TextEntry::make('registeredBy.phone')
                                ->label('Manager Phone')
                                ->copyable(),
                        ]),
                    ]),

                Section::make('Submitted Squad Roster')
                    ->icon('heroicon-o-users')
                    ->description('Roster players registered for this tournament.')
                    ->schema([
                        RepeatableEntry::make('roster_data')
                            ->label('')
                            ->schema([
                                Grid::make(4)->schema([
                                    TextEntry::make('name')->label('Full Name')->weight('bold'),
                                    TextEntry::make('role')->label('Role')->badge(),
                                    TextEntry::make('ign')->label('IGN / ID')->copyable(),
                                    TextEntry::make('ingame_role')->label('In-game Position')->placeholder('N/A'),
                                ]),
                            ])
                            ->columns(1)
                            ->columnSpanFull(),
                    ]),

                Section::make('Payment Receipt & Verification Logs')
                    ->icon('heroicon-o-photo')
                    ->schema([
                        Grid::make(2)->schema([
                            ImageEntry::make('payment_receipt_path')
                                ->label('Entry Fee Payment Receipt')
                                ->disk('public')
                                ->columnSpan(1),
                            TextEntry::make('notes')
                                ->label('Admin Verification Notes')
                                ->placeholder('No additional notes logged.')
                                ->columnSpan(1),
                        ]),
                    ]),
            ]);
    }
}
