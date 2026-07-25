<?php

namespace App\Filament\Resources\GameTitles\Pages;

use App\Filament\Resources\GameTitles\GameTitleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListGameTitles extends ListRecords
{
    protected static string $resource = GameTitleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Titles'),
            'moba' => Tab::make('⚔️ MOBA')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('game_type', '5v5_moba')),
            'battle_royale' => Tab::make('🪂 Battle Royale')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('game_type', 'battle_royale')),
            'fps' => Tab::make('🎯 Tactical FPS')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('game_type', 'fps')),
            'sports' => Tab::make('⚽ 1v1 Sports / Fighting')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('game_type', '1v1')),
        ];
    }
}
