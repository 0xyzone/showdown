<?php

namespace App\Filament\Resources\SponsorQueries\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SponsorQueryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Prospect Contact Information')
                    ->description('Contact person & company identity submitted from the website inquiry.')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Contact Person')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('company_name')
                                ->label('Company / Brand Name')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('email')
                                ->label('Email Address')
                                ->email()
                                ->required()
                                ->maxLength(255),
                            TextInput::make('phone')
                                ->label('Phone Number')
                                ->tel()
                                ->required()
                                ->maxLength(255),
                        ]),
                    ]),

                Section::make('Inquiry Processing & Proposal')
                    ->description('Current query status and proposal notes.')
                    ->icon('heroicon-o-chat-bubble-bottom-center-text')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('status')
                                ->label('Inquiry Status')
                                ->options([
                                    'pending' => '⏳ Pending (Under Review)',
                                    'contacted' => '📩 Contacted (Follow-up Sent)',
                                    'converted' => '🎉 Converted (Official Sponsor/Partner)',
                                    'rejected' => '❌ Rejected (Closed)',
                                ])
                                ->required()
                                ->default('pending')
                                ->columnSpanFull(),
                            Textarea::make('details')
                                ->label('Proposal Details & Requirements')
                                ->columnSpanFull()
                                ->rows(4),
                        ]),
                    ]),
            ]);
    }
}
