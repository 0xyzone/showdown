<?php

namespace App\Filament\Resources\Tournaments\Schemas;

use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\IconEntry;
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
                Section::make('Tournament Profile & Brand Identity')
                    ->icon('heroicon-o-trophy')
                    ->schema([
                        Grid::make(3)->schema([
                            ImageEntry::make('logo_path')
                                ->label('Logo Emblem')
                                ->disk('public')
                                ->height(140)
                                ->width(140)
                                ->extraImgAttributes(['class' => 'object-contain rounded-xl bg-slate-900 p-2 border border-slate-800'])
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
                                    ->label('Event Status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'draft' => 'secondary',
                                        'registration_open' => 'success',
                                        'ongoing' => 'info',
                                        'completed' => 'warning',
                                        'cancelled' => 'danger',
                                        default => 'secondary',
                                    }),
                                IconEntry::make('is_active')
                                    ->label('Active Event')
                                    ->boolean(),
                                ColorEntry::make('theme_color')
                                    ->label('Accent Theme Color'),
                                TextEntry::make('prize_pool_total')
                                    ->label('Total Prize Pool')
                                    ->money('NPR')
                                    ->weight('black')
                                    ->size('lg'),
                            ])->columnSpan(2),
                        ]),
                    ]),

                Section::make('Participating Game Disciplines')
                    ->icon('heroicon-o-puzzle-piece')
                    ->schema([
                        TextEntry::make('gameTitles.name')
                            ->label('Attached Game Titles')
                            ->badge()
                            ->color('primary')
                            ->separator(', ')
                            ->placeholder('No game titles assigned yet.'),
                    ]),

                Section::make('Challonge & Discord Integration Hub')
                    ->icon('heroicon-o-link')
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('challonge_url')
                                ->label('Challonge Bracket URL')
                                ->url(fn ($record) => $record->challonge_url, true)
                                ->placeholder('No Challonge bracket URL'),
                            TextEntry::make('challonge_embed_url')
                                ->label('Challonge Module Embed')
                                ->url(fn ($record) => $record->challonge_embed_url, true)
                                ->placeholder('No embed URL'),
                            TextEntry::make('discord_server_url')
                                ->label('Discord Server')
                                ->url(fn ($record) => $record->discord_server_url, true)
                                ->placeholder('No Discord link'),
                            TextEntry::make('discord_webhook_url')
                                ->label('Discord Webhook Announcements')
                                ->copyable()
                                ->placeholder('No Webhook configured'),
                            TextEntry::make('rules_doc_link')
                                ->label('Rulebook Document')
                                ->url(fn ($record) => $record->rules_doc_link, true)
                                ->placeholder('No rulebook attached'),
                            TextEntry::make('linktree_url')
                                ->label('Linktree Portal')
                                ->url(fn ($record) => $record->linktree_url, true)
                                ->placeholder('No Linktree link'),
                        ]),
                    ]),

                Section::make('Key Schedule Dates')
                    ->icon('heroicon-o-calendar')
                    ->schema([
                        Grid::make(4)->schema([
                            TextEntry::make('registration_start')
                                ->label('Registration Opens')
                                ->dateTime()
                                ->placeholder('TBD'),
                            TextEntry::make('registration_end')
                                ->label('Registration Closes')
                                ->dateTime()
                                ->placeholder('TBD'),
                            TextEntry::make('start_date')
                                ->label('Tournament Starts')
                                ->dateTime()
                                ->placeholder('TBD'),
                            TextEntry::make('end_date')
                                ->label('Tournament Ends')
                                ->dateTime()
                                ->placeholder('TBD'),
                        ]),
                    ]),

                Section::make('Tournament Description')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        TextEntry::make('description')
                            ->html()
                            ->placeholder('No additional description logged.'),
                    ]),
            ]);
    }
}
