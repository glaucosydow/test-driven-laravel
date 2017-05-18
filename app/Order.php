<?php

namespace App;

use App\Order;
use App\Ticket;
use App\Concert;
use Illuminate\Database\Eloquent\Model;
use App\Facades\OrderConfirmationNumber;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
            'confirmation_number' => OrderConfirmationNumber::generate(),
            'email' => $email,
            'amount' => $amount,
        ]);

        foreach ($tickets as $ticket) {
            $order->tickets()->save($ticket);
        }

        return $order;
    }

    /**
     * @param string $confirmationNumber
     *
     * @throws ModelNotFoundException
     *
     * @return Order
     */
    public static function findByConfirmationNumber(string $confirmationNumber)
    {
        return self::where('confirmation_number', $confirmationNumber)->firstOrFail();
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
            'confirmation_number' => $this->confirmation_number,
            'email' => $this->email,
            'ticket_quantity' => $this->ticketQuantity(),
            'amount' => $this->amount,
        ];
    }
}
