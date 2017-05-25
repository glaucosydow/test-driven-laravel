<?php

namespace App;

use App\Billing\Charge;
use App\Concert;
use App\Facades\OrderConfirmationNumber;
use App\Order;
use App\Ticket;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $guarded = [];

    /**
     * @param Collection $tickets
     * @param string     $email
     * @param Charge     $charge
     *
     * @return Order
     */
    public static function forTickets(Collection $tickets, string $email, Charge $charge): Order
    {
        $order = self::create([
            'confirmation_number' => OrderConfirmationNumber::generate(),
            'email' => $email,
            'amount' => $charge->amount(),
            'card_last_four' => $charge->cardLastFour(),
        ]);

        $order->tickets()->saveMany($tickets);

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
            'amount' => $this->amount,
            'tickets' => $this->tickets->map(function ($ticket) {
                return ['code' => $ticket->code];
            })->all(),
        ];
    }
}
