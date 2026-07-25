<?php

namespace App\Filament\Resources\Sponsors\Pages;

use App\Filament\Resources\Sponsors\SponsorResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListSponsors extends ListRecords
{
    protected static string $resource = SponsorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Sponsors'),
            'title' => Tab::make('👑 Title Sponsor')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('level', 'title')),
            'platinum' => Tab::make('💎 Platinum')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('level', 'platinum')),
            'gold' => Tab::make('🥇 Gold')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('level', 'gold')),
            'silver' => Tab::make('🛡️ Silver')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('level', 'silver')),
        ];
    }
}
