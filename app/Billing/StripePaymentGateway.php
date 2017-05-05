<?php

namespace App\Billing;

use App\Billing\PaymentFailedException;
use Illuminate\Support\Collection;
use Stripe\Charge;
use Stripe\Error\InvalidRequest;
use Stripe\Token;

class StripePaymentGateway implements PaymentGateway
{
    /**
     * @var string
     */
    protected $apiKey;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @param int    $amount
     * @param string $token
     *
     * @throws PaymentFailedException
     */
    public function charge(int $amount, string $token)
    {
        try {
            Charge::create(
                [
                    "amount" => $amount,
                    "currency" => "usd",
                    "source" => $token, // obtained with Stripe.js
                ],
                ['api_key' => $this->apiKey]
            );
        } catch (InvalidRequest $e) {
            throw new PaymentFailedException;
        }
    }

    /**
     * @return string
     */
    public function getValidTestToken(): string
    {
        return Token::create([
            "card" => [
                "number" => "4242424242424242",
                "exp_month" => 1,
                "exp_year" => \date('Y') + 1,
                "cvc" => "123",
            ],
        ], ['api_key' => $this->apiKey])->id;
    }

    /**
     * @param callable $callback
     *
     * @return Collection
     */
    public function newChargesDuring(callable $callback): Collection
    {
        $latestCharge = $this->lastCharge();

        $callback($this);

        return $this->newChargesSince($latestCharge)->pluck('amount');
    }

    /**
     * @return Charge
     */
    protected function lastCharge(): Charge
    {
        return array_first(
            Charge::all(
                ["limit" => 1],
                ['api_key' => $this->apiKey]
            )['data']
        );
    }

    /**
     * @param Charge $lastCharge
     *
     * @return Collection
     */
    private function newChargesSince(Charge $lastCharge): Collection
    {
        $newCharges = Charge::all(
            [
                "ending_before" => $lastCharge->id,
            ],
            ['api_key' => $this->apiKey]
        )['data'];

        return collect($newCharges);
    }
}
