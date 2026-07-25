<?php

namespace App\Filament\Resources\SponsorQueries\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SponsorQueryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('company_name'),
                TextEntry::make('email')
                    ->label('Email address'),
                TextEntry::make('phone'),
                TextEntry::make('details')
                    ->columnSpanFull(),
                TextEntry::make('status'),
                TextEntry::make('converted_type')
                    ->placeholder('-'),
                TextEntry::make('converted_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
