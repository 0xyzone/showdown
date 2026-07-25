<?php

namespace App\Filament\Resources\Partners\Pages;

use App\Filament\Resources\Partners\PartnerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListPartners extends ListRecords
{
    protected static string $resource = PartnerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Partners'),
            'major' => Tab::make('🌟 Major Partners')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('level', 'major')),
            'standard' => Tab::make('🤝 Standard Partners')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('level', 'standard')),
        ];
    }
}
