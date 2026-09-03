<?php

namespace App\Filament\Resources\Campaigns\Pages;

use App\Filament\Resources\Campaigns\CampaignResource;
use App\Filament\Widgets\CampaignCalendarWidget;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\Page;
use Filament\Support\Icons\Heroicon;

class CampaignCalendarPage extends Page
{
    protected static string $resource = CampaignResource::class;

    protected string $view = 'filament.resources.campaigns.pages.calendar';

    protected static ?string $title = 'Campaigns & Deliverables Calendar';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('backToList')
                ->label('Back to Table')
                ->url(CampaignResource::getUrl('index'))
                ->icon(Heroicon::OutlinedTableCells)
                ->color('gray'),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            CampaignCalendarWidget::class,
        ];
    }
}
