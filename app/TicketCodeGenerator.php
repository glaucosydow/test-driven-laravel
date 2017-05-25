<?php

namespace App;

interface TicketCodeGenerator
{
    /**
     * @return string
     */
    public function generate();
}
