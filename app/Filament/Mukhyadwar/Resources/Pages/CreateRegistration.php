<?php

namespace App\Filament\Mukhyadwar\Resources\Pages;

use App\Filament\Mukhyadwar\Resources\RegistrationResource;
use App\Mail\RegistrationSubmittedMail;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Mail;

class CreateRegistration extends CreateRecord
{
    protected static string $resource = RegistrationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['registered_by'] = auth('participant')->id();
        $data['status'] = 'pending';

        return $data;
    }

    protected function afterCreate(): void
    {
        $registration = $this->record;

        try {
            Mail::to(auth('participant')->user()->email)->send(new RegistrationSubmittedMail($registration));
        } catch (\Throwable $e) {
        }

        $admins = User::all();
        Notification::make()
            ->warning()
            ->title('New Tournament Registration')
            ->body("Team {$registration->team?->name} has registered for {$registration->tournament?->name}.")
            ->sendToDatabase($admins);
    }
}
