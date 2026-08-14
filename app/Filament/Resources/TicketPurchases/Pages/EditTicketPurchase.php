<?php

namespace App\Filament\Resources\TicketPurchases\Pages;

use App\Filament\Resources\TicketPurchases\TicketPurchaseResource;
use App\Models\TicketPurchase;
use App\Services\TicketService;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditTicketPurchase extends EditRecord
{
    protected static string $resource = TicketPurchaseResource::class;

    protected function afterSave(): void
    {
        /** @var TicketPurchase $record */
        $record = $this->record;

        if ($record->payment_status === 'paid') {
            app(TicketService::class)->issueTicketsForPurchase($record);

            Notification::make()
                ->title('Tickets Synchronized')
                ->body('Ticket records have been updated for confirmed payment.')
                ->success()
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
