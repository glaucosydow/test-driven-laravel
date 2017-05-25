<?php

namespace App;

interface OrderConfirmationNumberGenerator
{
    /**
     * @return string
     */
    public function generate();
}
