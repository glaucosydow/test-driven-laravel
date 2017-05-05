<?php

namespace App\Billing;

use Illuminate\Support\Collection;

interface PaymentGateway
{
    /**
     * Charge an amount for a client's token.
     *
     * @param int    $amount
     * @param string $token
     */
    public function charge(int $amount, string $token);

    /**
     * @return string
     */
    public function getValidTestToken(): string;

    /**
     * @param callable $callback
     * @param Collection
     */
    public function newChargesDuring(callable $callback): Collection;
}
