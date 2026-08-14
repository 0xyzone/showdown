<?php

namespace App\Filament\Resources\TicketPurchases\Pages;

use App\Filament\Resources\TicketPurchases\TicketPurchaseResource;
use App\Services\TicketService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewTicketPurchase extends ViewRecord
{
    protected static string $resource = TicketPurchaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download_pdf')
                ->label('Download Ticket PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->url(fn (): string => route('admin.ticket-purchases.pdf', $this->record))
                ->openUrlInNewTab()
                ->visible(fn (): bool => $this->record->payment_status === 'paid'),

            Action::make('confirm_payment')
                ->label('Confirm Payment & Generate Tickets')
                ->icon('heroicon-o-check-badge')
                ->color('primary')
                ->requiresConfirmation()
                ->action(function () {
                    app(TicketService::class)->issueTicketsForPurchase($this->record);

                    Notification::make()
                        ->title('Payment Confirmed')
                        ->body("{$this->record->quantity} tickets generated successfully.")
                        ->success()
                        ->send();

                    $this->fillForm();
                })
                ->visible(fn (): bool => $this->record->payment_status !== 'paid'),

            Action::make('view_receipt')
                ->label('Download Payment Receipt')
                ->icon('heroicon-o-document-text')
                ->color('gray')
                ->url(fn (): string => route('admin.ticket-purchases.receipt', $this->record))
                ->openUrlInNewTab()
                ->visible(fn (): bool => ! empty($this->record->payment_receipt_path)),

            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
