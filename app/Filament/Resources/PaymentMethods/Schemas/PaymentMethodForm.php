<?php

namespace App\Filament\Resources\PaymentMethods\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PaymentMethodForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Payment Method Details')
                    ->description('Define offline and online payment sources available for ticket sales and tournament fees.')
                    ->icon('heroicon-o-credit-card')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Payment Method Name')
                                ->placeholder('e.g. eSewa / Cash / Bank Transfer')
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (Set $set, ?string $state) => $set('code', Str::slug($state ?? '', '_')))
                                ->maxLength(255),

                            TextInput::make('code')
                                ->label('System Identifier Code')
                                ->placeholder('e.g. esewa_counter')
                                ->unique(ignoreRecord: true)
                                ->required()
                                ->maxLength(255),

                            TextInput::make('order')
                                ->label('Display Order')
                                ->numeric()
                                ->default(1)
                                ->required(),

                            Toggle::make('is_active')
                                ->label('Active / Available for Selection')
                                ->default(true)
                                ->required(),

                            Textarea::make('account_details')
                                ->label('Account Details / Cashier Instructions')
                                ->placeholder('e.g. Account No: 123456789, QR scan available at main entrance desk.')
                                ->rows(3)
                                ->columnSpanFull(),
                        ]),
                    ]),
            ]);
    }
}
