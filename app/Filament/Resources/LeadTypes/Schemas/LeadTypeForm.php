<?php

namespace App\Filament\Resources\LeadTypes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LeadTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
            ]);
    }
}
