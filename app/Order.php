<?php

namespace App;

use App\Concert;
use App\Order;
use App\Ticket;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $guarded = [];

    /**
     * @param Collection $tickets
     * @param string     $email
     * @param int        $amount
     *
     * @return Order
     */
    public static function forTickets(Collection $tickets, string $email, int $amount): Order
    {
        $order = self::create([
            'email' => $email,
            'amount' => $amount,
        ]);

        foreach ($tickets as $ticket) {
            $order->tickets()->save($ticket);
        }

        return $order;
    }

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
            'amount' => $this->amount,
        ];
    }
}
