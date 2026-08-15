<?php

namespace App\Filament\Resources\TicketPurchases\Schemas;

use App\Models\PaymentMethod;
use App\Models\TicketPackage;
use App\Models\Tournament;
use App\Models\TournamentEventDay;
use Filament\Forms\Components\CheckboxList;
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
                Section::make('Tournament Selection')
                    ->description('Select the tournament first to unlock packages, admission pricing, and available payment methods.')
                    ->icon('heroicon-o-trophy')
                    ->schema([
                        Select::make('tournament_id')
                            ->label('Select Tournament')
                            ->relationship('tournament', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                if ($state) {
                                    $t = Tournament::find($state);
                                    $set('ticket_package_id', null);
                                    $set('package_name', null);

                                    $price = $t ? (float) $t->ticket_price : 0;
                                    $set('unit_price', $price);
                                    $qty = (int) ($get('quantity') ?: 1);
                                    $set('total_amount', $qty * $price);

                                    // Reset selected payment method if it does not belong to new tournament
                                    $currentMethodId = $get('payment_method_id');
                                    if ($currentMethodId && $t) {
                                        $allowed = $t->paymentMethods()->where('payment_methods.id', $currentMethodId)->exists();
                                        if (! $allowed) {
                                            $set('payment_method_id', null);
                                            $set('payment_source', null);
                                        }
                                    }

                                    // Default all event days if tournament has them
                                    if ($t) {
                                        $allDayIds = $t->eventDays()->where('is_active', true)->pluck('id')->toArray();
                                        $set('custom_event_day_ids', $allDayIds);
                                    }
                                } else {
                                    $set('unit_price', 0);
                                    $set('total_amount', 0);
                                    $set('payment_method_id', null);
                                    $set('payment_source', null);
                                    $set('ticket_package_id', null);
                                    $set('package_name', null);
                                    $set('custom_event_day_ids', []);
                                }
                            }),
                    ]),

                Section::make('Ticket Package & Event Days Validity')
                    ->description('Choose a ticket package tier and event-day admission coverage.')
                    ->icon('heroicon-o-gift')
                    ->disabled(fn (Get $get): bool => ! filled($get('tournament_id')))
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('ticket_package_id')
                                ->label('Ticket Package / Admission Tier')
                                ->options(function (Get $get) {
                                    $tournamentId = $get('tournament_id');
                                    if (! $tournamentId) {
                                        return [];
                                    }

                                    return TicketPackage::where('tournament_id', $tournamentId)
                                        ->where('is_active', true)
                                        ->orderBy('order')
                                        ->get()
                                        ->mapWithKeys(fn ($pkg) => [$pkg->id => "{$pkg->name} (Rs. ".number_format($pkg->price, 2).')']);
                                })
                                ->searchable()
                                ->preload()
                                ->placeholder('Standard Tournament Admission (Default)')
                                ->live()
                                ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                    $tournamentId = $get('tournament_id');
                                    $qty = (int) ($get('quantity') ?: 1);

                                    if ($state) {
                                        $pkg = TicketPackage::with('eventDays')->find($state);
                                        if ($pkg) {
                                            $set('package_name', $pkg->name);
                                            $set('unit_price', (float) $pkg->price);
                                            $set('total_amount', $qty * (float) $pkg->price);

                                            if ($pkg->eventDays->isNotEmpty()) {
                                                $set('custom_event_day_ids', $pkg->eventDays->pluck('id')->toArray());
                                            }
                                        }
                                    } else {
                                        $set('package_name', 'Standard Admission');
                                        $t = Tournament::find($tournamentId);
                                        $price = $t ? (float) $t->ticket_price : 0;
                                        $set('unit_price', $price);
                                        $set('total_amount', $qty * $price);
                                    }
                                }),

                            CheckboxList::make('custom_event_day_ids')
                                ->label('Authorized Event Days')
                                ->options(function (Get $get) {
                                    $tournamentId = $get('tournament_id');
                                    if (! $tournamentId) {
                                        return [];
                                    }

                                    return TournamentEventDay::where('tournament_id', $tournamentId)
                                        ->where('is_active', true)
                                        ->orderBy('order')
                                        ->orderBy('event_date')
                                        ->get()
                                        ->mapWithKeys(fn ($day) => [$day->id => "{$day->day_name} (".($day->event_date ? $day->event_date->format('M d, Y') : '').')']);
                                })
                                ->columns(2)
                                ->helperText('Tick which specific days this ticket purchase is valid for.')
                                ->visible(function (Get $get): bool {
                                    $tournamentId = $get('tournament_id');
                                    if (! $tournamentId) {
                                        return false;
                                    }

                                    return TournamentEventDay::where('tournament_id', $tournamentId)->exists();
                                }),
                        ]),
                    ]),

                Section::make('Customer Details')
                    ->description('Customer account NOT required. Enter attendee contact information.')
                    ->icon('heroicon-o-user')
                    ->disabled(fn (Get $get): bool => ! filled($get('tournament_id')))
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('customer_name')
                                ->label('Customer Name')
                                ->placeholder('Full Name of Purchaser')
                                ->required()
                                ->disabled(fn (Get $get): bool => ! filled($get('tournament_id')))
                                ->maxLength(255),

                            TextInput::make('customer_phone')
                                ->label('Customer Phone')
                                ->placeholder('e.g. 98XXXXXXXX')
                                ->required()
                                ->tel()
                                ->disabled(fn (Get $get): bool => ! filled($get('tournament_id')))
                                ->maxLength(20),
                        ]),
                    ]),

                Section::make('Ticket Quantity & Pricing Calculation')
                    ->description('Ticket count and automatic total calculation.')
                    ->icon('heroicon-o-calculator')
                    ->disabled(fn (Get $get): bool => ! filled($get('tournament_id')))
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('quantity')
                                ->label('Number of Tickets')
                                ->numeric()
                                ->minValue(1)
                                ->maxValue(50)
                                ->default(1)
                                ->required()
                                ->disabled(fn (Get $get): bool => ! filled($get('tournament_id')))
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
                                ->disabled()
                                ->dehydrated()
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
                                ->disabled()
                                ->dehydrated()
                                ->extraInputAttributes(['class' => 'font-bold text-emerald-400'])
                                ->required(),
                        ]),
                    ]),

                Section::make('Payment Method & Receipt')
                    ->description('Select payment source allowed for this tournament and record receipt.')
                    ->icon('heroicon-o-banknotes')
                    ->disabled(fn (Get $get): bool => ! filled($get('tournament_id')))
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('payment_method_id')
                                ->label('Tournament Payment Method')
                                ->options(function (Get $get) {
                                    $tournamentId = $get('tournament_id');
                                    if (! $tournamentId) {
                                        return [];
                                    }

                                    $tournament = Tournament::find($tournamentId);
                                    if (! $tournament) {
                                        return [];
                                    }

                                    $methods = $tournament->paymentMethods()
                                        ->where('is_active', true)
                                        ->orderBy('order')
                                        ->get();

                                    if ($methods->isEmpty()) {
                                        $methods = PaymentMethod::where('is_active', true)->orderBy('order')->get();
                                    }

                                    return $methods->pluck('name', 'id');
                                })
                                ->searchable()
                                ->preload()
                                ->required()
                                ->disabled(fn (Get $get): bool => ! filled($get('tournament_id')))
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
                                    'paid' => '✅ Paid (Generate Admission Tickets)',
                                    'unpaid' => '⏳ Unpaid (Hold Order - No Tickets Issued)',
                                    'cancelled' => '❌ Cancelled / Void',
                                ])
                                ->required()
                                ->disabled(fn (Get $get): bool => ! filled($get('tournament_id')))
                                ->default('paid'),

                            TextInput::make('payment_reference')
                                ->label('Transaction ID / Ref #')
                                ->placeholder('e.g. Cash Counter / Txn #')
                                ->disabled(fn (Get $get): bool => ! filled($get('tournament_id')))
                                ->maxLength(255),

                            FileUpload::make('payment_receipt_path')
                                ->label('Payment Receipt (Image or PDF)')
                                ->disk('local')
                                ->directory('receipts')
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'application/pdf'])
                                ->maxSize(5120)
                                ->disabled(fn (Get $get): bool => ! filled($get('tournament_id')))
                                ->nullable(),

                            Textarea::make('notes')
                                ->label('Admin Notes')
                                ->placeholder('Special admission notes or cashier logs...')
                                ->rows(2)
                                ->disabled(fn (Get $get): bool => ! filled($get('tournament_id')))
                                ->columnSpanFull(),
                        ]),

                        Hidden::make('package_name'),
                        Hidden::make('payment_source'),
                        Hidden::make('seller_id')->default(fn () => auth()->id()),
                        Hidden::make('created_by')->default(fn () => auth()->id()),
                    ]),
            ]);
    }
}
