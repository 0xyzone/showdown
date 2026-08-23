<?php

namespace App\Filament\Mukhyadwar\Resources;

use App\Filament\Mukhyadwar\Resources\RegistrationResource\Pages\CreateRegistration;
use App\Filament\Mukhyadwar\Resources\RegistrationResource\Pages\ListRegistrations;
use App\Filament\Mukhyadwar\Resources\RegistrationResource\Pages\ViewRegistration;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\TournamentRegistration;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class RegistrationResource extends Resource
{
    protected static ?string $model = TournamentRegistration::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationLabel = 'Tournament Applications';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('registered_by', Auth::id());
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Tournament Registration Entry')
                    ->description('Select the target tournament, choose your team squad, and attach your entry fee payment receipt.')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('tournament_id')
                                ->label('Active Tournament')
                                ->options(Tournament::where('status', '!=', 'draft')->where(fn ($query) => $query->where('is_active', true)->orWhere('status', 'registration_open'))->pluck('name', 'id'))
                                ->required(),
                            Select::make('team_id')
                                ->label('My Team')
                                ->options(Team::where('manager_id', Auth::id())->pluck('name', 'id'))
                                ->required(),
                            FileUpload::make('payment_receipt_path')
                                ->label('Payment Receipt Screenshot')
                                ->image()
                                ->disk('public')
                                ->directory('receipts')
                                ->visibility('public')
                                ->required()
                                ->columnSpanFull(),
                        ]),
                    ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Application Details')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->schema([
                        Grid::make(4)->schema([
                            TextEntry::make('tournament.name')
                                ->label('Tournament')
                                ->weight('black')
                                ->size('lg'),
                            TextEntry::make('team.name')
                                ->label('Team Squad')
                                ->weight('bold'),
                            TextEntry::make('team.gameTitle.name')
                                ->label('Game Title Discipline')
                                ->badge()
                                ->color('info'),
                            TextEntry::make('status')
                                ->label('Status')
                                ->badge()
                                ->color(fn (string $state): string => match ($state) {
                                    'pending' => 'warning',
                                    'approved' => 'success',
                                    'rejected' => 'danger',
                                    'disqualified' => 'danger',
                                    default => 'secondary',
                                }),
                        ]),
                    ]),

                Section::make('Submitted Squad Roster')
                    ->icon('heroicon-o-users')
                    ->schema([
                        RepeatableEntry::make('roster_data')
                            ->label('')
                            ->schema([
                                Grid::make(4)->schema([
                                    TextEntry::make('name')->label('Full Name')->weight('bold'),
                                    TextEntry::make('role')->label('Role')->badge(),
                                    TextEntry::make('ign')->label('IGN / ID')->copyable(),
                                    TextEntry::make('ingame_role')->label('Position')->placeholder('N/A'),
                                ]),
                            ])
                            ->columns(1)
                            ->columnSpanFull(),
                    ]),

                Section::make('Payment Receipt & Notes')
                    ->icon('heroicon-o-photo')
                    ->schema([
                        Grid::make(2)->schema([
                            ImageEntry::make('payment_receipt_path')
                                ->label('Payment Receipt')
                                ->disk('public')
                                ->columnSpan(1),
                            TextEntry::make('notes')
                                ->label('Admin Verification Message')
                                ->placeholder('Under review by organizers.')
                                ->columnSpan(1),
                        ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('team.logo_path')
                    ->label('Crest')
                    ->disk('public')
                    ->square(),
                TextColumn::make('tournament.name')
                    ->label('Tournament')
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('team.name')
                    ->label('Team')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('team.gameTitle.name')
                    ->label('Game Title')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'disqualified' => 'danger',
                        default => 'secondary',
                    })
                    ->sortable(),
                ImageColumn::make('payment_receipt_path')
                    ->label('Receipt')
                    ->disk('public'),
                TextColumn::make('created_at')
                    ->label('Submitted On')
                    ->dateTime('M d, Y H:i'),
            ])
            ->filters([
                SelectFilter::make('tournament_id')
                    ->label('Filter by Tournament')
                    ->relationship('tournament', 'name'),
                SelectFilter::make('game_title')
                    ->label('Filter by Game Title')
                    ->relationship('team.gameTitle', 'name'),
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'disqualified' => 'Disqualified',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRegistrations::route('/'),
            'create' => CreateRegistration::route('/create'),
            'view' => ViewRegistration::route('/{record}'),
        ];
    }
}
