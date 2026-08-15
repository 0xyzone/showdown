<?php

namespace App\Filament\Resources\Tickets\Pages;

use App\Filament\Resources\Tickets\TicketResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTicket extends ViewRecord
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('verify_gate')
                ->label('Gate Verification Portal')
                ->icon('heroicon-o-qr-code')
                ->color('primary')
                ->url(fn (): string => route('ticket.verify', ['token' => $this->record->verification_token]))
                ->openUrlInNewTab(),

            DeleteAction::make(),
        ];
    }
}
