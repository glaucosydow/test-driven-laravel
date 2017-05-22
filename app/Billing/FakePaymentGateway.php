<?php

namespace App\Billing;

use Closure;
use Illuminate\Support\Collection;

class FakePaymentGateway implements PaymentGateway
{
    /**
     * @var Collection
     */
    protected $charges;

    /**
     * @var Collection
     */
    protected $tokens;

    /**
     * @var Closure
     */
    protected $beforeFirstChargeCallback;

    public function __construct()
    {
        $this->charges = collect();
        $this->tokens = collect();
    }

    /**
     * @param string $cardNumber
     *
     * @return string
     */
    public function getValidTestToken(string $cardNumber): string
    {
        $token = 'fake-tok_' . str_random(24);
        $this->tokens[$token] = $cardNumber;

        return $token;
    }

    /**
     * @param int    $amount
     * @param string $token
     */
    public function charge(int $amount, string $token)
    {
        if ($this->beforeFirstChargeCallback !== null) {
            $callBack = $this->beforeFirstChargeCallback;
            $this->beforeFirstChargeCallback = null;
            $callBack->__invoke($this);
        }

        if (! $this->tokens->has($token)) {
            throw new PaymentFailedException;
        }

        return $this->charges[] = new Charge([
            'amount' => $amount,
            'card_last_four' => $this->tokens[$token],
        ]);
    }

    /**
     * @param int    $amount
     * @param string $token
     */
    public function chargeBack(int $amount, string $token)
    {
        if ($token !== $this->getValidTestToken()) {
            throw new PaymentFailedException;
        }

        if ($this->totalCharges() === 0) {
            return;
        }

        $this->charges->push(-$amount);
    }

    /**
     * @return int
     */
    public function totalCharges()
    {
        return $this->charges->sum();
    }

    /**
     * @param Closure $callback
     */
    public function beforeFirstCharge(Closure $callback)
    {
        $this->beforeFirstChargeCallback = $callback;
    }

    /**
     * @param callable $callback
     *
     * @return Collection
     */
    public function newChargesDuring(callable $callback): Collection
    {
        $chargesFrom = $this->charges->count();
        $callback($this);

        return collect($this->charges->slice($chargesFrom)->reverse()->values());
    }
}
