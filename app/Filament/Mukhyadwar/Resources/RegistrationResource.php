<?php

namespace App\Filament\Mukhyadwar\Resources;

use App\Filament\Mukhyadwar\Resources\RegistrationResource\Pages\CreateRegistration;
use App\Filament\Mukhyadwar\Resources\RegistrationResource\Pages\ListRegistrations;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\TournamentRegistration;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
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
                                ->options(Tournament::where('is_active', true)->orWhere('status', 'registration_open')->pluck('name', 'id'))
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tournament.name')
                    ->label('Tournament')
                    ->weight('bold'),
                TextColumn::make('team.name')
                    ->label('Team'),
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
                    ->label('Submitted On')
                    ->dateTime('M d, Y H:i'),
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
