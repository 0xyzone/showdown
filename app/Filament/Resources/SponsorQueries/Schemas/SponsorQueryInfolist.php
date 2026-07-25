<?php

namespace App\Filament\Resources\SponsorQueries\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SponsorQueryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Inquiry Prospect Profile')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('company_name')
                                ->label('Company / Brand Name')
                                ->weight('black')
                                ->size('lg'),
                            TextEntry::make('name')
                                ->label('Contact Person')
                                ->weight('bold'),
                            TextEntry::make('status')
                                ->label('Inquiry Status')
                                ->badge()
                                ->color(fn (string $state): string => match ($state) {
                                    'pending' => 'warning',
                                    'contacted' => 'info',
                                    'converted' => 'success',
                                    'rejected' => 'danger',
                                    default => 'secondary',
                                }),
                            TextEntry::make('email')
                                ->label('Email Address')
                                ->copyable(),
                            TextEntry::make('phone')
                                ->label('Phone Number')
                                ->copyable(),
                            TextEntry::make('created_at')
                                ->label('Submitted On')
                                ->dateTime(),
                        ]),
                    ]),

                Section::make('Sponsorship Query Proposal')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        TextEntry::make('details')
                            ->label('Proposal Content')
                            ->columnSpanFull(),
                    ]),

                Section::make('Conversion Trace')
                    ->icon('heroicon-o-arrows-right-left')
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('converted_type')
                                ->label('Converted Entity Model')
                                ->placeholder('Not Converted Yet'),
                            TextEntry::make('converted_id')
                                ->label('Converted Entity ID')
                                ->placeholder('N/A'),
                        ]),
                    ]),
            ]);
    }
}
