<?php

namespace App\Filament\Resources\Sponsors\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SponsorInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Sponsor Brand Profile')
                    ->icon('heroicon-o-sparkles')
                    ->schema([
                        Grid::make(3)->schema([
                            ImageEntry::make('logo_url')
                                ->label('Brand Logo')
                                ->disk('public')
                                ->defaultImageUrl(asset('images/sponsor_placeholder.png'))
                                ->columnSpan(1),
                            Grid::make(2)->schema([
                                TextEntry::make('name')
                                    ->label('Brand / Sponsor Name')
                                    ->weight('black')
                                    ->size('lg'),
                                TextEntry::make('level')
                                    ->label('Hierarchy Tier')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'title' => 'warning',
                                        'platinum' => 'info',
                                        'gold' => 'success',
                                        'silver' => 'secondary',
                                        default => 'secondary',
                                    }),
                                TextEntry::make('website_url')
                                    ->label('Website URL')
                                    ->url(fn ($record) => $record->website_url, true)
                                    ->placeholder('No link provided'),
                                IconEntry::make('is_active')
                                    ->label('Website Visibility')
                                    ->boolean(),
                            ])->columnSpan(2),
                        ]),
                    ]),

                Section::make('MetaData & Inquiry Link')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('order')
                                ->label('Display Sort Order'),
                            TextEntry::make('sponsorQuery.name')
                                ->label('Origin Contact Person')
                                ->placeholder('Directly Created'),
                            TextEntry::make('created_at')
                                ->label('Added On')
                                ->dateTime(),
                        ]),
                    ]),
            ]);
    }
}
