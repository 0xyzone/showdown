<?php

namespace App\Filament\Resources\TournamentRegistrations\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TournamentRegistrationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Tournament & Team Assignment')
                    ->description('Select the target championship event and registered team squad.')
                    ->icon('heroicon-o-trophy')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('tournament_id')
                                ->label('Tournament')
                                ->relationship('tournament', 'name')
                                ->searchable()
                                ->required(),
                            Select::make('team_id')
                                ->label('Participating Team')
                                ->relationship('team', 'name')
                                ->searchable()
                                ->required(),
                            Select::make('registered_by')
                                ->label('Manager / Submitting Participant')
                                ->relationship('registeredBy', 'name')
                                ->searchable()
                                ->required(),
                            Select::make('status')
                                ->label('Registration Status')
                                ->options([
                                    'pending' => '⏳ Pending Admin Verification',
                                    'approved' => '✅ Approved & Verified',
                                    'rejected' => '❌ Rejected',
                                    'disqualified' => '🚫 Disqualified',
                                ])
                                ->required()
                                ->default('pending'),
                        ]),
                    ]),

                Section::make('Payment & Admin Notes')
                    ->description('Entry fee receipt screenshot and administrative verification notes.')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        FileUpload::make('payment_receipt_path')
                            ->label('Payment Receipt Image (Rs. 100 / person)')
                            ->image()
                            ->disk('public')
                            ->directory('receipts')
                            ->visibility('public')
                            ->columnSpanFull(),
                        Textarea::make('notes')
                            ->label('Administrative Notes / Verification Logs')
                            ->placeholder('e.g. Payment verified via eSewa / Khalti. Roster confirmed.')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
