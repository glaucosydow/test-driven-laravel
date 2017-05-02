<?php

namespace App;

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
