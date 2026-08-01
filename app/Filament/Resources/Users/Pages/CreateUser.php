<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Mail\OfficialMemberCredentialsMail;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected ?string $plainGeneratedPassword = null;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto-generate secure 12-character password
        $this->plainGeneratedPassword = Str::random(12);

        $data['password'] = Hash::make($this->plainGeneratedPassword);
        $data['must_change_password'] = true;

        return $data;
    }

    protected function afterCreate(): void
    {
        if ($this->plainGeneratedPassword && $this->record->email) {
            Mail::to($this->record->email)->send(
                new OfficialMemberCredentialsMail($this->record, $this->plainGeneratedPassword)
            );
        }
    }
}
