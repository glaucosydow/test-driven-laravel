<?php

namespace App;

use App\Ticket;

interface TicketCodeGenerator
{
    /**
     * @param Ticket $ticket
     *
     * @return string
     */
    public function generateFor(Ticket $ticket);
}
