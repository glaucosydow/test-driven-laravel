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
     * @param string $cardNumber
     *
     * @return string
     */
    public function getValidTestToken(string $cardNumber): string;

    /**
     * @param callable $callback
     *
     * @return Collection
     */
    public function newChargesDuring(callable $callback): Collection;
}
