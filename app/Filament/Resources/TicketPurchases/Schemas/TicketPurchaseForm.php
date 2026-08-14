<?php

namespace App\Filament\Resources\TicketPurchases\Schemas;

use App\Models\PaymentMethod;
use App\Models\Tournament;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class TicketPurchaseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Customer & Tournament')
                    ->description('Enter attendee details and tournament selection. Customer account NOT required.')
                    ->icon('heroicon-o-ticket')
                    ->schema([
                        Grid::make(3)->schema([
                            Select::make('tournament_id')
                                ->label('Tournament')
                                ->relationship('tournament', 'name')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->live()
                                ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                    if ($state) {
                                        $t = Tournament::find($state);
                                        $price = $t ? (float) $t->ticket_price : 0;
                                        $set('unit_price', $price);
                                        $qty = (int) ($get('quantity') ?: 1);
                                        $set('total_amount', $qty * $price);
                                    }
                                }),

                            TextInput::make('customer_name')
                                ->label('Customer Name')
                                ->placeholder('Full Name of Purchaser')
                                ->required()
                                ->maxLength(255),

                            TextInput::make('customer_phone')
                                ->label('Customer Phone')
                                ->placeholder('e.g. 98XXXXXXXX')
                                ->required()
                                ->tel()
                                ->maxLength(20),
                        ]),
                    ]),

                Section::make('Ticket Quantity & Pricing Calculation')
                    ->description('Specify ticket count and automatic amount calculation.')
                    ->icon('heroicon-o-calculator')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('quantity')
                                ->label('Number of Tickets')
                                ->numeric()
                                ->minValue(1)
                                ->maxValue(50)
                                ->default(1)
                                ->required()
                                ->live()
                                ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                    $qty = (int) ($state ?: 1);
                                    $unitPrice = (float) ($get('unit_price') ?: 0);
                                    $set('total_amount', $qty * $unitPrice);
                                }),

                            TextInput::make('unit_price')
                                ->label('Unit Price (Per Ticket)')
                                ->numeric()
                                ->prefix('Rs.')
                                ->required()
                                ->live()
                                ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                    $unitPrice = (float) ($state ?: 0);
                                    $qty = (int) ($get('quantity') ?: 1);
                                    $set('total_amount', $qty * $unitPrice);
                                }),

                            TextInput::make('total_amount')
                                ->label('Total Payable Amount')
                                ->numeric()
                                ->prefix('Rs.')
                                ->readOnly()
                                ->extraInputAttributes(['class' => 'font-bold text-emerald-400'])
                                ->required(),
                        ]),
                    ]),

                Section::make('Manual Payment Confirmation & Receipt')
                    ->description('Select payment source and record receipt.')
                    ->icon('heroicon-o-banknotes')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('payment_method_id')
                                ->label('Payment Source / Method')
                                ->options(PaymentMethod::where('is_active', true)->orderBy('order')->pluck('name', 'id'))
                                ->searchable()
                                ->preload()
                                ->required()
                                ->live()
                                ->afterStateUpdated(function (Set $set, ?string $state) {
                                    if ($state) {
                                        $method = PaymentMethod::find($state);
                                        $set('payment_source', $method?->name);
                                    }
                                }),

                            Select::make('payment_status')
                                ->label('Payment Confirmation Status')
                                ->options([
                                    'paid' => '✅ Paid (Issue Tickets Immediately)',
                                    'unpaid' => '⏳ Unpaid (Hold Order - No Tickets Issued)',
                                    'cancelled' => '❌ Cancelled / Void',
                                ])
                                ->required()
                                ->default('paid'),

                            TextInput::make('payment_reference')
                                ->label('Transaction ID / Ref #')
                                ->placeholder('e.g. Cash Counter / eSewa Txn #')
                                ->maxLength(255),

                            FileUpload::make('payment_receipt_path')
                                ->label('Payment Receipt (Image or PDF)')
                                ->disk('local')
                                ->directory('receipts')
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'application/pdf'])
                                ->maxSize(5120)
                                ->nullable(),

                            Textarea::make('notes')
                                ->label('Admin Notes')
                                ->placeholder('Special admission notes or cashier logs...')
                                ->rows(2)
                                ->columnSpanFull(),
                        ]),

                        Hidden::make('payment_source'),
                        Hidden::make('created_by')->default(fn () => auth()->id()),
                    ]),
            ]);
    }
}
