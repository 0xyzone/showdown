<?php

namespace App\Filament\Resources\Tournaments\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class TournamentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Tournament Configuration')
                    ->tabs([
                        Tab::make('General Details')
                            ->icon('heroicon-o-trophy')
                            ->schema([
                                Grid::make(3)->schema([
                                    TextInput::make('name')
                                        ->label('Tournament Name')
                                        ->required()
                                        ->maxLength(255),
                                    TextInput::make('slug')
                                        ->label('Slug')
                                        ->required()
                                        ->maxLength(255),
                                    Select::make('gameTitles')
                                        ->label('Participating Game Titles')
                                        ->relationship('gameTitles', 'name')
                                        ->multiple()
                                        ->preload()
                                        ->required(),
                                    TextInput::make('season_version')
                                        ->label('Season / Edition')
                                        ->default('2026 Vol-I')
                                        ->required(),
                                    Select::make('status')
                                        ->label('Tournament Status')
                                        ->options([
                                            'draft' => 'Draft',
                                            'registration_open' => 'Registration Open',
                                            'ongoing' => 'Ongoing',
                                            'completed' => 'Completed',
                                            'cancelled' => 'Cancelled',
                                        ])
                                        ->required()
                                        ->default('registration_open'),
                                    Toggle::make('is_active')
                                        ->label('Active Homepage Tournament')
                                        ->helperText('Enable to display this tournament as the featured event on the website.')
                                        ->default(true),
                                    FileUpload::make('logo_path')
                                        ->label('Tournament Logo')
                                        ->image()
                                        ->disk('public')
                                        ->directory('tournaments'),
                                    FileUpload::make('banner_path')
                                        ->label('Tournament Banner')
                                        ->image()
                                        ->disk('public')
                                        ->directory('tournaments')
                                        ->columnSpan(2),
                                    RichEditor::make('description')
                                        ->label('Tournament Description')
                                        ->columnSpanFull(),
                                ]),
                            ]),

                        Tab::make('Challonge & Discord Integration')
                            ->icon('heroicon-o-link')
                            ->schema([
                                Grid::make(2)->schema([
                                    TextInput::make('challonge_url')
                                        ->label('Challonge Bracket URL')
                                        ->placeholder('https://challonge.com/outlaw_showdown_2026')
                                        ->url(),
                                    TextInput::make('challonge_embed_url')
                                        ->label('Challonge Module Embed URL')
                                        ->placeholder('https://challonge.com/outlaw_showdown_2026/module')
                                        ->url(),
                                    TextInput::make('discord_server_url')
                                        ->label('Discord Community Server URL')
                                        ->placeholder('https://discord.gg/outlawshowdown')
                                        ->url(),
                                    TextInput::make('discord_webhook_url')
                                        ->label('Discord Webhook Announcement URL')
                                        ->placeholder('https://discord.com/api/webhooks/...')
                                        ->url(),
                                    TextInput::make('rules_doc_link')
                                        ->label('Rulebook Document URL')
                                        ->url(),
                                    TextInput::make('linktree_url')
                                        ->label('Linktree / Portal URL')
                                        ->url(),
                                ]),
                                Repeater::make('custom_links')
                                    ->label('Custom Information Links')
                                    ->schema([
                                        TextInput::make('title')->required(),
                                        TextInput::make('url')->url()->required(),
                                    ])
                                    ->columns(2)
                                    ->columnSpanFull(),
                            ]),

                        Tab::make('Rules & Schedule Dates')
                            ->icon('heroicon-o-calendar')
                            ->schema([
                                Grid::make(2)->schema([
                                    DateTimePicker::make('registration_start')
                                        ->label('Registration Opens'),
                                    DateTimePicker::make('registration_end')
                                        ->label('Registration Closes'),
                                    DateTimePicker::make('start_date')
                                        ->label('Tournament Start Date'),
                                    DateTimePicker::make('end_date')
                                        ->label('Tournament End Date'),
                                ]),
                            ]),

                        Tab::make('Prize Pool Distribution')
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                TextInput::make('prize_pool_total')
                                    ->label('Total Prize Pool Amount (NPR)')
                                    ->numeric()
                                    ->prefix('Rs.')
                                    ->default(0),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
