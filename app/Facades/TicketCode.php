<?php

namespace App\Facades;

use App\TicketCodeGenerator;
use Illuminate\Support\Facades\Facade;

class TicketCode extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return TicketCodeGenerator::class;
    }

    /**
     * Specify the class/interface that Mockery should mock.
     *
     * @return string
     */
    protected static function getMockableClass()
    {
        return static::getFacadeAccessor();
    }
}
