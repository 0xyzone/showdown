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
                Section::make('Sponsor Inquiry Details')
                    ->description('Contact and brand proposal submitted by prospect.')
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
                            Select::make('status')
                                ->label('Inquiry Status')
                                ->options([
                                    'pending' => 'Pending',
                                    'contacted' => 'Contacted',
                                    'converted' => 'Converted',
                                    'rejected' => 'Rejected',
                                ])
                                ->required()
                                ->default('pending'),
                            Textarea::make('details')
                                ->label('Query Proposal Details')
                                ->columnSpanFull()
                                ->rows(4),
                        ]),
                    ]),
            ]);
    }
}
