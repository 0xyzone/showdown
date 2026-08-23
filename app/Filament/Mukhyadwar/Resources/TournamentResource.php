<?php

namespace App\Filament\Mukhyadwar\Resources;

use App\Filament\Mukhyadwar\Resources\TournamentResource\Pages\ListTournaments;
use App\Filament\Mukhyadwar\Resources\TournamentResource\Pages\ViewTournament;
use App\Models\Team;
use App\Models\TeamPlayer;
use App\Models\Tournament;
use App\Models\TournamentRegistration;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TournamentResource extends Resource
{
    protected static ?string $model = Tournament::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-trophy';

    protected static ?string $navigationLabel = 'Tournaments';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Tournament Details')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('name')->readOnly(),
                            TextInput::make('season_version')->readOnly(),
                            TextInput::make('status')->readOnly(),
                            TextInput::make('formatted_entry_fee')->label('Entry Fee')->readOnly(),
                            TextInput::make('prize_pool_total')->prefix('Rs.')->readOnly(),
                        ]),
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
                    ->label('Tournament')
                    ->weight('bold')
                    ->searchable(),
                TextColumn::make('season_version')
                    ->label('Edition')
                    ->badge(),
                TextColumn::make('gameTitles.name')
                    ->label('Game Titles')
                    ->badge()
                    ->color('info'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'registration_open' => 'success',
                        'ongoing' => 'warning',
                        'completed' => 'gray',
                        default => 'secondary',
                    }),
                TextColumn::make('formatted_entry_fee')
                    ->label('Entry Fee')
                    ->badge()
                    ->color('warning'),
                TextColumn::make('prize_pool_total')
                    ->label('Prize Pool')
                    ->money('NPR'),
                TextColumn::make('registration_end')
                    ->label('Registration Closes')
                    ->dateTime('M d, Y H:i'),
            ])
            ->actions([
                TableAction::make('registerTeam')
                    ->label('Register Team')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->color('success')
                    ->visible(fn (Tournament $record): bool => in_array($record->status, ['registration_open', 'ongoing']) || $record->is_active)
                    ->form(fn (Tournament $record) => static::getRegistrationFormSchema($record))
                    ->action(fn (array $data, Tournament $record) => static::processTeamRegistration($data, $record)),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->where('status', '!=', 'draft'));
    }

    public static function getRegistrationFormSchema(Tournament $tournament): array
    {
        $userTeams = Team::where('manager_id', Auth::id())->pluck('name', 'id');

        return [
            Select::make('team_id')
                ->label('Select Your Team')
                ->options($userTeams)
                ->required()
                ->live()
                ->helperText('Choose one of your registered teams to enter this tournament.'),

            CheckboxList::make('selected_players')
                ->label('Select Roster Players')
                ->options(function (callable $get) {
                    $teamId = $get('team_id');
                    if (! $teamId) {
                        return [];
                    }

                    return TeamPlayer::where('team_id', $teamId)
                        ->get()
                        ->mapWithKeys(fn (TeamPlayer $player) => [
                            $player->id => "{$player->full_name} ({$player->role} - IGN: {$player->ign})",
                        ]);
                })
                ->required()
                ->columns(2)
                ->helperText('Select main players, substitutes, coach, and manager for your squad.'),

            FileUpload::make('payment_receipt_path')
                ->label('Entry Fee Payment Receipt Screenshot')
                ->image()
                ->disk('public')
                ->directory('registration-receipts')
                ->required(),

            Textarea::make('notes')
                ->label('Additional Registration Notes')
                ->placeholder('Any special requests or team preferences...')
                ->rows(2),
        ];
    }

    public static function processTeamRegistration(array $data, Tournament $tournament): void
    {
        $team = Team::with('gameTitle')->find($data['team_id']);
        if (! $team) {
            Notification::make()->title('Invalid team selected.')->danger()->send();

            return;
        }

        // Check for existing registration
        $alreadyRegistered = TournamentRegistration::where('tournament_id', $tournament->id)
            ->where('team_id', $team->id)
            ->exists();

        if ($alreadyRegistered) {
            Notification::make()
                ->title('Already Registered!')
                ->body("Your team '{$team->name}' is already registered for {$tournament->name}.")
                ->warning()
                ->send();

            return;
        }

        $selectedPlayerIds = $data['selected_players'] ?? [];
        $players = TeamPlayer::whereIn('id', $selectedPlayerIds)->get();

        $gameTitle = $team->gameTitle;
        $minMain = $gameTitle?->min_main_players ?? 5;
        $maxSubs = $gameTitle?->max_substitutes ?? 2;

        $mainCount = $players->where('role', 'main_player')->count();
        $subCount = $players->where('role', 'substitute')->count();
        $coachCount = $players->where('role', 'coach')->count();
        $managerCount = $players->where('role', 'manager')->count();

        // Game title specific roster validation
        if ($mainCount < $minMain) {
            Notification::make()
                ->title('Registration Error: Incomplete Roster')
                ->body("Game title '{$gameTitle?->name}' requires at least {$minMain} main players. You selected {$mainCount}.")
                ->danger()
                ->send();

            return;
        }

        if ($subCount > $maxSubs) {
            Notification::make()
                ->title('Registration Error: Exceeded Substitute Limit')
                ->body("Maximum allowed substitutes for '{$gameTitle?->name}' is {$maxSubs}. You selected {$subCount}.")
                ->danger()
                ->send();

            return;
        }

        if ($coachCount > 1) {
            Notification::make()
                ->title('Registration Error: Exceeded Coach Limit')
                ->body('Maximum allowed coaches is 1 per squad.')
                ->danger()
                ->send();

            return;
        }

        if ($managerCount > 1) {
            Notification::make()
                ->title('Registration Error: Exceeded Manager Limit')
                ->body('Maximum allowed managers is 1 per squad.')
                ->danger()
                ->send();

            return;
        }

        // Build roster data payload
        $rosterData = $players->map(fn (TeamPlayer $p) => [
            'id' => $p->id,
            'name' => $p->full_name,
            'role' => $p->role,
            'ign' => $p->ign,
            'ingame_role' => $p->ingame_role,
        ])->values()->toArray();

        TournamentRegistration::create([
            'tournament_id' => $tournament->id,
            'team_id' => $team->id,
            'registered_by' => Auth::id(),
            'status' => 'pending',
            'roster_data' => $rosterData,
            'payment_receipt_path' => $data['payment_receipt_path'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        Notification::make()
            ->title('Registration Submitted!')
            ->body("Your team '{$team->name}' has successfully registered for {$tournament->name}. Status: Pending Verification.")
            ->success()
            ->send();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTournaments::route('/'),
            'view' => ViewTournament::route('/{record}'),
        ];
    }
}
