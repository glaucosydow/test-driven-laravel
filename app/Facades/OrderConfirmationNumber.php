<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use App\OrderConfirmationNumberGenerator;

class OrderConfirmationNumber extends Facade
{
	/**
	 * @return string
	 */
    protected static function getFacadeAccessor()
    {
        return OrderConfirmationNumberGenerator::class;
    }
}