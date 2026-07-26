<?php

namespace App\Filament\Mukhyadwar\Resources;

use App\Filament\Mukhyadwar\Resources\TeamResource\Pages\CreateTeam;
use App\Filament\Mukhyadwar\Resources\TeamResource\Pages\EditTeam;
use App\Filament\Mukhyadwar\Resources\TeamResource\Pages\ListTeams;
use App\Models\Team;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TeamResource extends Resource
{
    protected static ?string $model = Team::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'My Teams';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('manager_id', Auth::id());
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Esports Squad Profile')
                    ->description('Select game title and update team identity.')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('name')
                                ->label('Team Name')
                                ->placeholder('e.g. Outlaw Clan')
                                ->required(),
                            TextInput::make('tag')
                                ->label('Team Tag / Prefix')
                                ->placeholder('e.g. OTL')
                                ->required(),
                            Select::make('game_title_id')
                                ->label('Game Title')
                                ->relationship('gameTitle', 'name')
                                ->preload()
                                ->searchable()
                                ->required(),
                            TextInput::make('country')
                                ->label('Country / Location')
                                ->default('Nepal')
                                ->required(),
                            FileUpload::make('logo_path')
                                ->label('Team Crest / Logo')
                                ->image()
                                ->disk('public')
                                ->directory('teams')
                                ->columnSpanFull(),
                        ]),
                    ]),

                Section::make('Squad Players & Staff Roster')
                    ->description('Add and update team players, in-game profiles, photos, and contact info.')
                    ->schema([
                        Repeater::make('players')
                            ->relationship('players')
                            ->schema([
                                Grid::make(3)->schema([
                                    TextInput::make('full_name')
                                        ->label('Full Name')
                                        ->required(),
                                    Select::make('role')
                                        ->label('Squad Role')
                                        ->options([
                                            'main_player' => 'Main Player',
                                            'substitute' => 'Substitute Player',
                                            'coach' => 'Coach',
                                            'manager' => 'Manager',
                                        ])
                                        ->default('main_player')
                                        ->required(),
                                    DatePicker::make('date_of_birth')
                                        ->label('Date of Birth'),
                                    TextInput::make('ign')
                                        ->label('In-Game Name / ID')
                                        ->placeholder('e.g. OutlawPro#1234')
                                        ->required(),
                                    TextInput::make('ingame_role')
                                        ->label('In-Game Role / Position')
                                        ->placeholder('e.g. Jungler, Entry Fragger, IGL'),
                                    TextInput::make('whatsapp_number')
                                        ->label('WhatsApp Number')
                                        ->tel(),
                                    TextInput::make('email')
                                        ->label('Email Address')
                                        ->email(),
                                    TextInput::make('discord_id')
                                        ->label('Discord Tag / ID'),
                                    TextInput::make('citizenship_number')
                                        ->label('Citizenship / NID Number (Optional)'),
                                ]),

                                Grid::make(4)->schema([
                                    FileUpload::make('front_photo_path')
                                        ->label('Front Photo (Hands Folded)')
                                        ->image()
                                        ->disk('public')
                                        ->directory('team-players/photos')
                                        ->helperText('Front facing player photo with hands folded.'),
                                    FileUpload::make('ingame_profile_screenshot_path')
                                        ->label('In-Game Profile Screenshot')
                                        ->image()
                                        ->disk('public')
                                        ->directory('team-players/screenshots')
                                        ->helperText('Screenshot of player in-game profile.'),
                                    FileUpload::make('citizenship_front_path')
                                        ->label('Citizenship Front (Optional)')
                                        ->image()
                                        ->disk('public')
                                        ->directory('team-players/citizenship'),
                                    FileUpload::make('citizenship_back_path')
                                        ->label('Citizenship Back (Optional)')
                                        ->image()
                                        ->disk('public')
                                        ->directory('team-players/citizenship'),
                                ]),
                            ])
                            ->itemLabel(fn (array $state): ?string => ($state['full_name'] ?? 'Player').' ('.($state['ign'] ?? 'No IGN').')')
                            ->columns(1)
                            ->defaultItems(5)
                            ->collapsible()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo_path')
                    ->label('Logo')
                    ->disk('public')
                    ->square(),
                TextColumn::make('name')
                    ->label('Team Name')
                    ->weight('bold')
                    ->searchable(),
                TextColumn::make('tag')
                    ->label('Tag')
                    ->badge(),
                TextColumn::make('gameTitle.name')
                    ->label('Game Title')
                    ->badge()
                    ->color('info'),
                TextColumn::make('players_count')
                    ->label('Roster Count')
                    ->counts('players')
                    ->badge()
                    ->color('success'),
                TextColumn::make('country')
                    ->label('Country'),
                TextColumn::make('created_at')
                    ->label('Created On')
                    ->dateTime('M d, Y'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTeams::route('/'),
            'create' => CreateTeam::route('/create'),
            'edit' => EditTeam::route('/{record}/edit'),
        ];
    }
}
