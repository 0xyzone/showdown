<?php

namespace App\Filament\Resources\TicketPurchases\Pages;

use App\Filament\Resources\TicketPurchases\TicketPurchaseResource;
use App\Models\TicketPurchase;
use App\Services\TicketService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateTicketPurchase extends CreateRecord
{
    protected static string $resource = TicketPurchaseResource::class;

    protected function afterCreate(): void
    {
        /** @var TicketPurchase $record */
        $record = $this->record;

        if ($record->payment_status === 'paid') {
            app(TicketService::class)->issueTicketsForPurchase($record);

            Notification::make()
                ->title('Tickets Issued Successfully')
                ->body("{$record->quantity} admission tickets have been generated for {$record->customer_name}.")
                ->success()
                ->send();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
