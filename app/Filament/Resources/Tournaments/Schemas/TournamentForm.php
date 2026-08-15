<?php

namespace App\Filament\Resources\Tournaments\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class TournamentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Tournament Configuration')
                    ->tabs([
                        Tab::make('General Details & Theme')
                            ->icon('heroicon-o-trophy')
                            ->schema([
                                Grid::make(3)->schema([
                                    TextInput::make('name')
                                        ->label('Tournament Name')
                                        ->required()
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state ?? '')))
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
                                    ColorPicker::make('theme_color')
                                        ->label('Tournament Primary Accent Color')
                                        ->helperText('Select or type a hex accent theme color for the homepage (e.g. #10B981 Emerald).')
                                        ->hex()
                                        ->default('#10b981')
                                        ->required(),
                                    FileUpload::make('logo_path')
                                        ->label('Tournament Logo (1:1 Ratio, PNG)')
                                        ->image()
                                        ->imageEditor()
                                        ->imageEditorAspectRatios([
                                            '1:1',
                                        ])
                                        ->acceptedFileTypes(['image/png'])
                                        ->imageCropAspectRatio('1:1')
                                        ->disk('public')
                                        ->directory('tournaments'),
                                    FileUpload::make('banner_path')
                                        ->label('Tournament Banner (16:9 Ratio)')
                                        ->image()
                                        ->imageEditor()
                                        ->imageEditorAspectRatios([
                                            '16:9',
                                        ])
                                        ->imageCropAspectRatio('16:9')
                                        ->disk('public')
                                        ->directory('tournaments'),
                                    RichEditor::make('description')
                                        ->label('Tournament Description')
                                        ->columnSpanFull(),
                                ]),
                            ]),

                        Tab::make('Hero Headline Content')
                            ->icon('heroicon-o-sparkles')
                            ->schema([
                                Grid::make(1)->schema([
                                    TextInput::make('hero_headline')
                                        ->label('Homepage Hero Main Headline')
                                        ->placeholder('e.g. UNLEASH THE LEGEND, CLAIM YOUR GLORY')
                                        ->maxLength(255),
                                    Textarea::make('hero_subheadline')
                                        ->label('Homepage Hero Subheadline / Tagline')
                                        ->placeholder("Nepal's premier national esports championship stage is live! Register your squad...")
                                        ->rows(3),
                                ]),
                            ]),

                        Tab::make('Registration & Admission Pricing')
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                Grid::make(3)->schema([
                                    TextInput::make('ticket_price')
                                        ->label('Admission Ticket Price (NPR)')
                                        ->numeric()
                                        ->prefix('Rs.')
                                        ->default(150.00)
                                        ->helperText('Default ticket unit price for audience admission.')
                                        ->required(),
                                    TextInput::make('entry_fee')
                                        ->label('Registration Entry Fee Amount (NPR)')
                                        ->numeric()
                                        ->prefix('Rs.')
                                        ->default(100.00)
                                        ->required(),
                                    TextInput::make('entry_fee_suffix')
                                        ->label('Entry Fee Suffix / Unit')
                                        ->placeholder('e.g. person, team, head, player')
                                        ->default('person')
                                        ->helperText('Displayed alongside fee (e.g. Rs. 100/person).')
                                        ->required(),
                                ]),
                            ]),

                        Tab::make('Sponsors, Partners & Payment Methods')
                            ->icon('heroicon-o-building-office-2')
                            ->schema([
                                Grid::make(2)->schema([
                                    Select::make('sponsors')
                                        ->label('Select Tournament Official Sponsors')
                                        ->relationship('sponsors', 'name')
                                        ->multiple()
                                        ->preload()
                                        ->helperText('Select the official sponsors assigned to this tournament.'),
                                    Select::make('partners')
                                        ->label('Select Tournament Event Partners')
                                        ->relationship('partners', 'name')
                                        ->multiple()
                                        ->preload()
                                        ->helperText('Select the media and event partners assigned to this tournament.'),
                                    Select::make('paymentMethods')
                                        ->label('Allowed Payment Methods for Ticket Sales & Registration')
                                        ->relationship('paymentMethods', 'name')
                                        ->multiple()
                                        ->preload()
                                        ->helperText('Choose which payment methods are accepted for this tournament.')
                                        ->columnSpanFull(),
                                ]),
                            ]),

                        Tab::make('Discord & External Links')
                            ->icon('heroicon-o-link')
                            ->schema([
                                Grid::make(2)->schema([
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

                        Tab::make('Event Schedule Days')
                            ->icon('heroicon-o-calendar-days')
                            ->schema([
                                Repeater::make('eventDays')
                                    ->relationship('eventDays')
                                    ->schema([
                                        TextInput::make('day_name')
                                            ->label('Day Label / Name')
                                            ->placeholder('e.g. Day 1 - Group Stage')
                                            ->required()
                                            ->columnSpan(2),

                                        DatePicker::make('event_date')
                                            ->label('Event Date')
                                            ->required()
                                            ->columnSpan(1),

                                        TextInput::make('order')
                                            ->label('Order #')
                                            ->numeric()
                                            ->default(1)
                                            ->columnSpan(1),

                                        Toggle::make('is_active')
                                            ->label('Active Day')
                                            ->default(true)
                                            ->columnSpan(1),

                                        TextInput::make('notes')
                                            ->label('Day Notes / Highlights')
                                            ->placeholder('Special matches, gate timings, VIP entry...')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(5)
                                    ->defaultItems(1)
                                    ->addActionLabel('Add Event Day')
                                    ->reorderable('order')
                                    ->columnSpanFull(),
                            ]),

                        Tab::make('Dates')
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
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
