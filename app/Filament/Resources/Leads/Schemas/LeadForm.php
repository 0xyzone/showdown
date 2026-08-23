<?php

namespace App\Filament\Resources\Leads\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class LeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('user_id')
                    ->default(auth()->id()),

                Grid::make(['default' => 1, 'lg' => 3])
                    ->schema([
                        Grid::make(1)
                            ->schema([
                                Section::make('Classification')
                                    ->icon('heroicon-o-tag')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            Select::make('lead_type_id')
                                                ->label('Lead Type')
                                                ->relationship('lead_type', 'name')
                                                ->preload()
                                                ->searchable()
                                                ->createOptionForm([
                                                    TextInput::make('name')
                                                        ->required(),
                                                ])
                                                ->required(),
                                            Select::make('lead_status_id')
                                                ->label('Status')
                                                ->relationship('lead_status', 'name')
                                                ->preload()
                                                ->searchable()
                                                ->createOptionForm([
                                                    TextInput::make('name')
                                                        ->required(),
                                                ])
                                                ->required(),
                                        ]),
                                    ]),

                                Section::make('Company & Contact')
                                    ->icon('heroicon-o-building-office-2')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextInput::make('company_name')
                                                ->label('Company / Organization')
                                                ->required()
                                                ->maxLength(255),
                                            TextInput::make('contact_name')
                                                ->label('Contact Person')
                                                ->required()
                                                ->maxLength(255),
                                            TextInput::make('phone')
                                                ->label('Phone Number')
                                                ->tel()
                                                ->required()
                                                ->maxLength(255),
                                            TextInput::make('email')
                                                ->label('Email Address')
                                                ->email()
                                                ->maxLength(255),
                                        ]),
                                    ]),

                                Section::make('Location & Notes')
                                    ->icon('heroicon-o-map-pin')
                                    ->schema([
                                        Grid::make(1)->schema([
                                            Textarea::make('address')
                                                ->label('Location / Address')
                                                ->rows(2),
                                            TextInput::make('gmap_link')
                                                ->label('Google Maps Link')
                                                ->url()
                                                ->placeholder('https://maps.google.com/...'),
                                            Textarea::make('notes')
                                                ->label('General Lead Notes')
                                                ->rows(3),
                                        ]),
                                    ]),
                            ])
                            ->columnSpan(['default' => 1, 'lg' => 1]),

                        Grid::make(1)
                            ->schema([
                                Section::make('Follow-ups Tracking')
                                    ->icon('heroicon-o-chat-bubble-left-right')
                                    ->description('Track historical follow-up dates, outcomes, and remarks.')
                                    ->schema([
                                        Repeater::make('followups')
                                            ->relationship('followups')
                                            ->schema([
                                                Grid::make(2)->schema([
                                                    DatePicker::make('followup_date')
                                                        ->label('Follow-up Date')
                                                        ->default(now())
                                                        ->required()
                                                        ->native(false),
                                                    Select::make('user_id')
                                                        ->label('Followed up by')
                                                        ->relationship('user', 'name')
                                                        ->default(fn () => Auth::id())
                                                        ->searchable()
                                                        ->disabled()
                                                        ->dehydrated()
                                                        ->preload(),
                                                    Textarea::make('remarks')
                                                        ->label('Remarks & Outcomes')
                                                        ->placeholder('Enter follow-up discussion points, next action items...')
                                                        ->required()
                                                        ->autosize()
                                                        ->columnSpanFull(),
                                                ]),
                                            ])
                                            ->itemLabel(fn (array $state): ?string => isset($state['followup_date']) ? 'Follow-up: '.date('M d, Y', strtotime($state['followup_date'])) : 'New Follow-up')
                                            ->collapsible()
                                            ->defaultItems(0)
                                            ->addActionLabel('Add Follow-up')
                                            ->columnSpanFull(),
                                    ]),
                            ])
                            ->columnSpan(['default' => 1, 'lg' => 2]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
