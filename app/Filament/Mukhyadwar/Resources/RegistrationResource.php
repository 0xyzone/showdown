<?php

namespace App\Filament\Mukhyadwar\Resources;

use App\Filament\Mukhyadwar\Resources\Pages\CreateRegistration;
use App\Filament\Mukhyadwar\Resources\Pages\ListRegistrations;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\TournamentRegistration;
use BackedEnum;
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

class RegistrationResource extends Resource
{
    protected static ?string $model = TournamentRegistration::class;

    protected static ?string $navigationLabel = 'Tournament Entries';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('registered_by', auth('participant')->id());
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Tournament Entry Details')
                    ->description('Select the target tournament and your team roster.')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('tournament_id')
                                ->label('Tournament')
                                ->options(Tournament::where('status', 'registration_open')->pluck('name', 'id'))
                                ->required(),
                            Select::make('team_id')
                                ->label('Your Team')
                                ->options(Team::where('manager_id', auth('participant')->id())->pluck('name', 'id'))
                                ->required(),
                            FileUpload::make('payment_receipt_path')
                                ->label('Payment Receipt Screenshot (Rs. 100 / person)')
                                ->image()
                                ->disk('public')
                                ->directory('receipts')
                                ->required()
                                ->columnSpanFull(),
                        ]),
                    ]),

                Section::make('Player Roster Details')
                    ->schema([
                        Repeater::make('roster_data')
                            ->label('Starting Roster & Substitutes')
                            ->schema([
                                TextInput::make('player_name')->label('Full Name')->required(),
                                TextInput::make('ign')->label('In-Game Name (IGN)')->required(),
                                Select::make('role')
                                    ->label('Roster Role')
                                    ->options([
                                        'starter' => 'Starter',
                                        'substitute' => 'Substitute',
                                        'coach' => 'Coach',
                                    ])
                                    ->required(),
                            ])
                            ->columns(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tournament.name')
                    ->label('Tournament')
                    ->weight('bold'),
                TextColumn::make('team.name')
                    ->label('Team Name'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'disqualified' => 'danger',
                        default => 'secondary',
                    }),
                ImageColumn::make('payment_receipt_path')
                    ->label('Receipt')
                    ->disk('public'),
                TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime('M d, Y'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRegistrations::route('/'),
            'create' => CreateRegistration::route('/create'),
        ];
    }
}
