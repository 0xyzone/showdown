<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable([
    'tournament_id',
    'ticket_package_id',
    'package_name',
    'created_by',
    'seller_id',
    'payment_method_id',
    'order_number',
    'customer_name',
    'customer_phone',
    'quantity',
    'unit_price',
    'total_amount',
    'payment_status',
    'payment_source',
    'payment_reference',
    'payment_receipt_path',
    'paid_at',
    'notes',
])]
class TicketPurchase extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (TicketPurchase $purchase) {
            if (empty($purchase->order_number)) {
                $purchase->order_number = 'ORD-'.strtoupper(Str::random(4)).'-'.strtoupper(Str::random(4));
            }
            if (empty($purchase->seller_id)) {
                $purchase->seller_id = $purchase->created_by ?? (auth()->check() ? auth()->id() : null);
            }
            if (empty($purchase->created_by)) {
                $purchase->created_by = $purchase->seller_id ?? (auth()->check() ? auth()->id() : null);
            }
        });
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function ticketPackage(): BelongsTo
    {
        return $this->belongsTo(TicketPackage::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}
