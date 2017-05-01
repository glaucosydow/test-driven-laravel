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
     * @var Closure
     */
    protected $beforeFirstChargeCallback;

    public function __construct()
    {
        $this->charges = collect();
    }

    public function getValidTestToken()
    {
        return 'valid-token';
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

        if ($token !== $this->getValidTestToken()) {
            throw new PaymentFailedException;
        }

        $this->charges->push($amount);
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
}
