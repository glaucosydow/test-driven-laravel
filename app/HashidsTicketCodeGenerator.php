<?php

namespace App;

use App\Ticket;

class HashidsTicketCodeGenerator implements TicketCodeGenerator
{
    private $hashids;

    /**
     * @param string $salt
     */
    public function __construct(string $salt)
    {
        $this->hashids = new \Hashids\Hashids($salt, 6, 'ABCDEFGHIJKLMNOPQRSTUVXYZ');
    }

    /**
     * @param Ticket $ticket
     *
     * @return string
     */
    public function generateFor(Ticket $ticket): string
    {
        return $this->hashids->encode($ticket->id);
    }
}
