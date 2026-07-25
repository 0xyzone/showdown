<?php

namespace App\Filament\Mukhyadwar\Resources;

use App\Filament\Mukhyadwar\Resources\TeamResource\Pages\CreateTeam;
use App\Filament\Mukhyadwar\Resources\TeamResource\Pages\EditTeam;
use App\Filament\Mukhyadwar\Resources\TeamResource\Pages\ListTeams;
use App\Models\Team;
use Filament\Forms\Components\FileUpload;
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
                    ->description('Create or edit your team squad identity.')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Team Name')
                                ->placeholder('e.g. Outlaw Clan')
                                ->required(),
                            TextInput::make('tag')
                                ->label('Team Tag / Prefix')
                                ->placeholder('e.g. OTL')
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
