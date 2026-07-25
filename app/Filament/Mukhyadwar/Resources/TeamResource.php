<?php

namespace App\Filament\Mukhyadwar\Resources;

use App\Filament\Mukhyadwar\Resources\Pages\CreateTeam;
use App\Filament\Mukhyadwar\Resources\Pages\EditTeam;
use App\Filament\Mukhyadwar\Resources\Pages\ListTeams;
use App\Models\Team;
use BackedEnum;
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

class TeamResource extends Resource
{
    protected static ?string $model = Team::class;

    protected static ?string $navigationLabel = 'My Teams';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('manager_id', auth('participant')->id());
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Team Identity')
                    ->description('Create or edit your esports team profile.')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Team Name')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('tag')
                                ->label('Team Tag / Abbreviation')
                                ->placeholder('e.g. OTL')
                                ->maxLength(10),
                            TextInput::make('country')
                                ->label('Country')
                                ->default('Nepal')
                                ->required(),
                            FileUpload::make('logo_path')
                                ->label('Team Logo')
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
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('tag')
                    ->label('Tag')
                    ->badge(),
                TextColumn::make('country')
                    ->label('Country'),
                TextColumn::make('created_at')
                    ->label('Created')
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
