<?php

namespace App\Filament\Resources\Leads\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LeadInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 1, 'lg' => 2])
                    ->schema([
                        Grid::make(1)
                            ->schema([
                                Section::make('Lead Summary & Classification')
                                    ->icon('heroicon-o-briefcase')
                                    ->schema([
                                        Grid::make(3)->schema([
                                            TextEntry::make('company_name')
                                                ->label('Company / Organization')
                                                ->weight('black')
                                                ->size('lg'),
                                            TextEntry::make('lead_type.name')
                                                ->label('Lead Type')
                                                ->badge()
                                                ->color('info'),
                                            TextEntry::make('lead_status.name')
                                                ->label('Current Status')
                                                ->badge()
                                                ->color('warning'),
                                            TextEntry::make('user.name')
                                                ->label('Assigned / Lead Owner')
                                                ->icon('heroicon-m-user'),
                                            TextEntry::make('created_at')
                                                ->label('Created On')
                                                ->dateTime('M d, Y H:i'),
                                            TextEntry::make('updated_at')
                                                ->label('Last Updated')
                                                ->dateTime('M d, Y H:i'),
                                        ]),
                                    ]),

                                Section::make('Contact & Location')
                                    ->icon('heroicon-o-phone')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextEntry::make('contact_name')
                                                ->label('Contact Person')
                                                ->weight('bold'),
                                            TextEntry::make('phone')
                                                ->label('Phone Number')
                                                ->icon('heroicon-m-phone')
                                                ->copyable(),
                                            TextEntry::make('email')
                                                ->label('Email Address')
                                                ->icon('heroicon-m-envelope')
                                                ->copyable()
                                                ->placeholder('-'),
                                            TextEntry::make('gmap_link')
                                                ->label('Google Maps')
                                                ->url(fn ($record) => $record->gmap_link, true)
                                                ->icon('heroicon-m-map-pin')
                                                ->placeholder('No map link provided'),
                                            TextEntry::make('address')
                                                ->label('Street / Location Address')
                                                ->placeholder('No address specified')
                                                ->columnSpanFull(),
                                        ]),
                                    ]),

                                Section::make('Internal Notes')
                                    ->icon('heroicon-o-document-text')
                                    ->schema([
                                        TextEntry::make('notes')
                                            ->label('')
                                            ->placeholder('No general notes recorded.')
                                            ->columnSpanFull(),
                                    ]),
                            ])
                            ->columnSpan(1),

                        Grid::make(1)
                            ->schema([
                                Section::make('Scheduled Meetings')
                                    ->icon('heroicon-o-calendar-days')
                                    ->description('Upcoming & past calendar meetings.')
                                    ->schema([
                                        RepeatableEntry::make('meetings')
                                            ->label('')
                                            ->placeholder('No meetings scheduled yet.')
                                            ->schema([
                                                Grid::make(1)->schema([
                                                    Grid::make(2)->schema([
                                                        TextEntry::make('title')
                                                            ->label('Agenda / Title')
                                                            ->weight('bold')
                                                            ->columnSpanFull(),
                                                        TextEntry::make('meeting_start')
                                                            ->label('Start')
                                                            ->dateTime('M d, Y H:i')
                                                            ->badge()
                                                            ->color('info'),
                                                        TextEntry::make('meeting_end')
                                                            ->label('End')
                                                            ->dateTime('M d, Y H:i')
                                                            ->badge()
                                                            ->color('secondary'),
                                                        TextEntry::make('status')
                                                            ->label('Status')
                                                            ->badge()
                                                            ->color(fn (string $state): string => match ($state) {
                                                                'scheduled' => 'warning',
                                                                'completed' => 'success',
                                                                'cancelled' => 'danger',
                                                                'rescheduled' => 'info',
                                                                default => 'secondary',
                                                            }),
                                                        TextEntry::make('meeting_location_type')
                                                            ->label('Type')
                                                            ->badge()
                                                            ->formatStateUsing(fn ($state) => match ($state) {
                                                                'online_meet' => 'Google Meet / Online',
                                                                'in_person' => 'In-Person',
                                                                'phone' => 'Phone Call',
                                                                default => ucfirst($state),
                                                            }),
                                                        TextEntry::make('meeting_link')
                                                            ->label('Meeting Link')
                                                            ->url(fn ($record) => $record->meeting_link, true)
                                                            ->placeholder('No link provided')
                                                            ->icon('heroicon-m-video-camera')
                                                            ->columnSpanFull(),
                                                        TextEntry::make('user.name')
                                                            ->label('Host')
                                                            ->icon('heroicon-m-user')
                                                            ->placeholder('Unassigned'),
                                                    ]),
                                                    TextEntry::make('notes')
                                                        ->label('Agenda Notes')
                                                        ->markdown()
                                                        ->placeholder('No notes'),
                                                ]),
                                            ])
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Follow-ups History')
                                    ->icon('heroicon-o-clock')
                                    ->description('Chronological record of discussions & follow-ups.')
                                    ->schema([
                                        RepeatableEntry::make('followups')
                                            ->label('')
                                            ->placeholder('No follow-up entries logged yet.')
                                            ->schema([
                                                Grid::make(1)->schema([
                                                    Grid::make(2)->schema([
                                                        TextEntry::make('followup_date')
                                                            ->label('Date')
                                                            ->date('M d, Y')
                                                            ->badge()
                                                            ->color('primary'),
                                                        TextEntry::make('user.name')
                                                            ->label('By')
                                                            ->placeholder('System / Unassigned')
                                                            ->icon('heroicon-m-user-circle'),
                                                    ]),
                                                    TextEntry::make('remarks')
                                                        ->label('Remarks')
                                                        ->markdown(),
                                                ]),
                                            ])
                                            ->columnSpanFull(),
                                    ]),
                            ])
                            ->columnSpan(1),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
