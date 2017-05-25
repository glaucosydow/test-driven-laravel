<?php

namespace App;

use App\Billing\PaymentGateway;
use App\Order;
use Illuminate\Database\Eloquent\Collection;

class Reservation
{
    /**
     * @var Collection
     */
    protected $tickets;

    /**
     * @var string
     */
    protected $email;

    public function __construct(Collection $tickets, string $email)
    {
        $this->tickets = $tickets;
        $this->email = $email;
    }

    /**
     * @return int
     */
    public function totalCost()
    {
        return $this->tickets->sum('price');
    }

    /**
     * Cancel a reservation.
     */
    public function cancel()
    {
        foreach ($this->tickets as $ticket) {
            $ticket->release();
        }
    }

    /**
     * @param PaymentGateway $paymentGateway
     * @param string         $paymentToken
     *
     * @return Order
     */
    public function complete(PaymentGateway $paymentGateway, string $paymentToken): Order
    {
        $charge = $paymentGateway->charge($this->totalCost(), $paymentToken);

        return Order::forTickets($this->tickets(), $this->email(), $charge);
    }

    /**
     * @return Collection
     */
    public function tickets(): Collection
    {
        return $this->tickets;
    }

    /**
     * @return string
     */
    public function email(): string
    {
        return $this->email;
    }
}
