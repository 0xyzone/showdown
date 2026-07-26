<?php

namespace App\Filament\Resources\TournamentRegistrations\Pages;

use App\Filament\Resources\TournamentRegistrations\TournamentRegistrationResource;
use App\Mail\RegistrationStatusUpdatedMail;
use App\Models\TournamentRegistration;
use App\Services\DiscordService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Mail;

class ViewTournamentRegistration extends ViewRecord
{
    protected static string $resource = TournamentRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('approve')
                ->label('Approve Registration')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn (TournamentRegistration $record) => $record->status !== 'approved')
                ->schema([
                    Textarea::make('notes')
                        ->label('Approval Note / Confirmation Details')
                        ->placeholder('e.g. Roster verified, payment received.'),
                ])
                ->action(function (array $data, TournamentRegistration $record) {
                    $record->update([
                        'status' => 'approved',
                        'notes' => $data['notes'] ?? $record->notes,
                    ]);

                    if ($participant = $record->registeredBy) {
                        Notification::make()
                            ->success()
                            ->title('Registration Approved!')
                            ->body("Your team {$record->team?->name} has been approved for {$record->tournament?->name}.")
                            ->sendToDatabase($participant);

                        try {
                            Mail::to($participant->email)->send(new RegistrationStatusUpdatedMail($record, $data['notes'] ?? ''));
                        } catch (\Throwable $e) {
                        }
                    }

                    // Send Discord Webhook announcement
                    DiscordService::sendRegistrationNotification($record);

                    Notification::make()
                        ->success()
                        ->title('Registration approved & Discord webhook dispatched!')
                        ->send();
                }),

            Action::make('deny')
                ->label('Deny Registration')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn (TournamentRegistration $record) => $record->status !== 'rejected')
                ->schema([
                    Textarea::make('notes')
                        ->label('Rejection / Denial Reason')
                        ->required()
                        ->placeholder('State why the registration is being denied (e.g. invalid receipt).'),
                ])
                ->action(function (array $data, TournamentRegistration $record) {
                    $record->update([
                        'status' => 'rejected',
                        'notes' => $data['notes'],
                    ]);

                    if ($participant = $record->registeredBy) {
                        Notification::make()
                            ->danger()
                            ->title('Registration Denied')
                            ->body("Your team {$record->team?->name} registration for {$record->tournament?->name} was denied.")
                            ->sendToDatabase($participant);

                        try {
                            Mail::to($participant->email)->send(new RegistrationStatusUpdatedMail($record, $data['notes']));
                        } catch (\Throwable $e) {
                        }
                    }

                    // Send Discord Webhook announcement
                    DiscordService::sendRegistrationNotification($record);

                    Notification::make()
                        ->warning()
                        ->title('Registration denied.')
                        ->send();
                }),

            EditAction::make(),
        ];
    }
}
