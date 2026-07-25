<?php

namespace App\Filament\Resources\TournamentRegistrations\Schemas;

use Filament\Infolists\Components\ImageEntry;
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
                Section::make('Registration Overview')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('tournament.name')
                                ->label('Tournament')
                                ->weight('black')
                                ->size('lg'),
                            TextEntry::make('team.name')
                                ->label('Team Name')
                                ->weight('bold'),
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
                            TextEntry::make('registeredBy.name')
                                ->label('Submitted By (Manager)'),
                            TextEntry::make('registeredBy.email')
                                ->label('Manager Email')
                                ->copyable(),
                            TextEntry::make('created_at')
                                ->label('Submitted On')
                                ->dateTime(),
                        ]),
                    ]),

                Section::make('Payment Receipt & Notes')
                    ->icon('heroicon-o-photo')
                    ->schema([
                        Grid::make(2)->schema([
                            ImageEntry::make('payment_receipt_path')
                                ->label('Payment Receipt Screenshot')
                                ->disk('public')
                                ->columnSpan(1),
                            TextEntry::make('notes')
                                ->label('Admin Notes')
                                ->placeholder('No additional notes logged.')
                                ->columnSpan(1),
                        ]),
                    ]),
            ]);
    }
}
