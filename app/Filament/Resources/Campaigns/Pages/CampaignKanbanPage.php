<?php

namespace App\Filament\Resources\Campaigns\Pages;

use App\Filament\Resources\Campaigns\CampaignResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\Page;
use Filament\Support\Icons\Heroicon;

class CampaignKanbanPage extends Page
{
    protected static string $resource = CampaignResource::class;

    protected string $view = 'filament.resources.campaigns.pages.kanban';

    protected static ?string $title = 'Campaigns Kanban Pipeline';

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
}
