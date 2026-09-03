<?php

namespace App\Filament\Resources\Leads\Pages;

use App\Filament\Resources\Leads\LeadResource;
use App\Models\Lead;
use App\Models\LeadStatus;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Enums\Width;
use Illuminate\Database\Eloquent\Builder;

class ListLeads extends ListRecords
{
    protected static string $resource = LeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->slideOver()
                ->modalWidth(Width::SevenExtraLarge),
        ];
    }

    public function getTabs(): array
    {
        $statuses = LeadStatus::all();
        $tabs = [];
        $tabs['all'] = Tab::make('All');
        foreach ($statuses as $status) {
            $tabs[$status->name] = Tab::make($status->name)
                ->modifyQueryUsing(fn (Builder $query) => $query->where('lead_status_id', $status->id))
                ->badge(Lead::where('lead_status_id', $status->id)->count());
        }
        return $tabs;
    }
}
