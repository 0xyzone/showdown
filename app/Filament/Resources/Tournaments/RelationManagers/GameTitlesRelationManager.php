<?php

namespace App\Filament\Resources\Tournaments\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GameTitlesRelationManager extends RelationManager
{
    protected static string $relationship = 'gameTitles';

    protected static ?string $title = 'Game Titles & Prize Pool Allocation';

    public static function recalculatePrizePool(Get $get, Set $set): void
    {
        $distribution = $get('prize_distribution');
        if (! is_array($distribution)) {
            $decoded = is_string($distribution) ? json_decode($distribution, true) : null;
            $distribution = is_array($decoded) ? $decoded : [];
        }

        $total = 0;
        foreach ($distribution as $value) {
            $cleanValue = preg_replace('/[^0-9.]/', '', (string) $value);
            if (is_numeric($cleanValue)) {
                $total += (float) $cleanValue;
            }
        }

        $set('prize_pool', $total);
    }

    protected function updateOverallTournamentPrizePool(): void
    {
        $tournament = $this->getOwnerRecord();
        if (! $tournament) {
            return;
        }

        $overallTotal = 0;
        foreach ($tournament->gameTitles as $game) {
            $overallTotal += (float) ($game->pivot->prize_pool ?? 0);
        }

        $tournament->updateQuietly([
            'prize_pool_total' => $overallTotal,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                KeyValue::make('prize_distribution')
                    ->label('Prize Pool Distribution Breakdown (Key / Value Pairs)')
                    ->keyLabel('Placement Rank (e.g., 1st Place, 2nd Place, MVP)')
                    ->valueLabel('Prize Amount (NPR) (e.g., 150000, 75000)')
                    ->keyPlaceholder('e.g. 1st Place')
                    ->valuePlaceholder('e.g. 150000')
                    ->live()
                    ->afterStateUpdated(fn (Get $get, Set $set) => static::recalculatePrizePool($get, $set))
                    ->columnSpanFull()
                    ->helperText('Add placement ranks and prize amounts. The total game prize pool will automatically calculate based on these values.'),

                TextInput::make('prize_pool')
                    ->label('Auto-Calculated Total Game Prize Pool (NPR)')
                    ->numeric()
                    ->prefix('Rs.')
                    ->readOnly()
                    ->default(0)
                    ->helperText('Automatically calculated sum of all placement prize amounts entered above.'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Game Title')
                    ->weight(FontWeight::Bold)
                    ->searchable(),
                TextColumn::make('game_type')
                    ->label('Category')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => strtoupper(str_replace('_', ' ', $state ?? ''))),
                TextColumn::make('prize_pool')
                    ->label('Calculated Prize Pool')
                    ->money('NPR')
                    ->sortable(),
                TextColumn::make('prize_distribution')
                    ->label('Distribution Breakdown')
                    ->formatStateUsing(function ($state) {
                        if (empty($state)) {
                            return 'None configured';
                        }
                        $data = is_array($state) ? $state : json_decode((string) $state, true);
                        if (! is_array($data) || empty($data)) {
                            return (string) $state;
                        }
                        $items = [];
                        foreach ($data as $key => $val) {
                            $items[] = "{$key}: Rs. ".number_format((float) preg_replace('/[^0-9.]/', '', (string) $val));
                        }

                        return implode(' • ', $items);
                    })
                    ->limit(50)
                    ->placeholder('None configured'),
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Add Game Title to Tournament')
                    ->preloadRecordSelect()
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        KeyValue::make('prize_distribution')
                            ->label('Prize Pool Distribution Breakdown (Key / Value Pairs)')
                            ->keyLabel('Placement Rank (e.g., 1st Place, 2nd Place, MVP)')
                            ->valueLabel('Prize Amount (NPR) (e.g., 150000, 75000)')
                            ->keyPlaceholder('e.g. 1st Place')
                            ->valuePlaceholder('e.g. 150000')
                            ->live()
                            ->afterStateUpdated(fn (Get $get, Set $set) => static::recalculatePrizePool($get, $set))
                            ->columnSpanFull()
                            ->helperText('Add placement ranks and prize amounts. Total prize pool calculates automatically.'),
                        TextInput::make('prize_pool')
                            ->label('Auto-Calculated Total Game Prize Pool (NPR)')
                            ->numeric()
                            ->prefix('Rs.')
                            ->readOnly()
                            ->default(0),
                    ])
                    ->after(fn () => $this->updateOverallTournamentPrizePool()),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Edit Prize Pool Allocation')
                    ->after(fn () => $this->updateOverallTournamentPrizePool()),
                DetachAction::make()
                    ->after(fn () => $this->updateOverallTournamentPrizePool()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make()
                        ->after(fn () => $this->updateOverallTournamentPrizePool()),
                ]),
            ]);
    }
}
