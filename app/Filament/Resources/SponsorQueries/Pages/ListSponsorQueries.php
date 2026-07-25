<?php

namespace App\Filament\Resources\SponsorQueries\Pages;

use App\Filament\Resources\SponsorQueries\SponsorQueryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListSponsorQueries extends ListRecords
{
    protected static string $resource = SponsorQueryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Queries'),
            'pending' => Tab::make('Pending Response')
                ->icon('heroicon-o-envelope')
                ->badge(fn () => $this->getModel()::where('status', 'pending')->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending')),
            'contacted' => Tab::make('Followed Up / Contacted')
                ->icon('heroicon-o-paper-airplane')
                ->badgeColor('info')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'contacted')),
            'converted' => Tab::make('Converted Brand Sponsors')
                ->icon('heroicon-o-sparkles')
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'converted')),
            'rejected' => Tab::make('Rejected')
                ->icon('heroicon-o-x-mark')
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'rejected')),
        ];
    }
}
