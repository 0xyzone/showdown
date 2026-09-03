<?php

namespace App\Filament\Resources\Campaigns\Pages;

use App\Enums\CampaignStatus;
use App\Filament\Resources\Campaigns\CampaignResource;
use App\Models\Campaign;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;

class ListCampaigns extends ListRecords
{
    protected static string $resource = CampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('kanbanView')
                ->label('Kanban Pipeline')
                ->icon(Heroicon::OutlinedViewColumns)
                ->color('amber')
                ->url(CampaignResource::getUrl('kanban')),
            Action::make('timelineView')
                ->label('Timeline / Gantt')
                ->icon(Heroicon::OutlinedChartBar)
                ->color('info')
                ->url(CampaignResource::getUrl('timeline')),
            Action::make('calendarView')
                ->label('Calendar View')
                ->icon(Heroicon::OutlinedCalendarDays)
                ->color('purple')
                ->url(CampaignResource::getUrl('calendar')),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [];
        $tabs['all'] = Tab::make('All Campaigns')
            ->badge(Campaign::count());

        foreach (CampaignStatus::cases() as $status) {
            $count = Campaign::where('status', $status->value)->count();
            if ($count > 0) {
                $tabs[$status->value] = Tab::make($status->getLabel())
                    ->modifyQueryUsing(fn (Builder $query) => $query->where('status', $status->value))
                    ->badge($count);
            }
        }

        return $tabs;
    }
}
