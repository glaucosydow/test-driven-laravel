<?php

namespace App;

use App\Concert;
use App\Ticket;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $guarded = [];

    /**
     * @return HasMany
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * @return int
     */
    public function ticketQuantity(): int
    {
        return $this->tickets()->count();
    }

    /**
     * @return BelongsTo
     */
    public function concert(): BelongsTo
    {
        return $this->belongsTo(Concert::class);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'ticket_quantity' => $this->ticketQuantity(),
            'amount' => $this->ticketQuantity() * $this->concert->ticket_price,
        ];
    }
}
