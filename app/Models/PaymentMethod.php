<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'code',
    'account_details',
    'is_active',
    'order',
])]
class PaymentMethod extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'order' => 'integer',
        ];
    }

    public function ticketPurchases(): HasMany
    {
        return $this->hasMany(TicketPurchase::class);
    }

    public function tournaments(): BelongsToMany
    {
        return $this->belongsToMany(Tournament::class);
    }
}
