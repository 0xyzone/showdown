<?php

namespace App\Filament\Resources\Tournaments\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TournamentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Tournament Profile')
                    ->icon('heroicon-o-trophy')
                    ->schema([
                        Grid::make(3)->schema([
                            ImageEntry::make('logo_path')
                                ->label('Logo')
                                ->disk('public')
                                ->defaultImageUrl(asset('images/sponsor_placeholder.png')),
                            Grid::make(2)->schema([
                                TextEntry::make('name')
                                    ->label('Tournament Name')
                                    ->weight('black')
                                    ->size('lg'),
                                TextEntry::make('season_version')
                                    ->label('Season / Edition')
                                    ->badge()
                                    ->color('primary'),
                                TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'draft' => 'secondary',
                                        'registration_open' => 'success',
                                        'ongoing' => 'info',
                                        'completed' => 'warning',
                                        'cancelled' => 'danger',
                                        default => 'secondary',
                                    }),
                                TextEntry::make('prize_pool_total')
                                    ->label('Total Prize Pool')
                                    ->money('NPR')
                                    ->weight('black'),
                            ])->columnSpan(2),
                        ]),
                    ]),

                Section::make('Social & Rulebook Links')
                    ->icon('heroicon-o-link')
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('rules_doc_link')
                                ->label('Rulebook Document')
                                ->url(fn ($record) => $record->rules_doc_link, true)
                                ->placeholder('No rulebook attached'),
                            TextEntry::make('discord_server_url')
                                ->label('Discord Server')
                                ->url(fn ($record) => $record->discord_server_url, true)
                                ->placeholder('No Discord link'),
                            TextEntry::make('linktree_url')
                                ->label('Linktree Hub')
                                ->url(fn ($record) => $record->linktree_url, true)
                                ->placeholder('No Linktree link'),
                        ]),
                    ]),
            ]);
    }
}
