<?php

namespace App\Billing;

use App\Billing\PaymentFailedException;
use Illuminate\Support\Collection;
use Stripe\Charge as StripeCharge;
use Stripe\Error\InvalidRequest;
use Stripe\Token;

class StripePaymentGateway implements PaymentGateway
{
    const TEST_CARD = '5555555555554444';

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
            $stripeCharge = StripeCharge::create(
                [
                    "amount" => $amount,
                    "currency" => "usd",
                    "source" => $token, // obtained with Stripe.js
                ],
                ['api_key' => $this->apiKey]
            );

            return new Charge([
                'amount' => $stripeCharge['amount'],
                'card_last_four' => $stripeCharge['source']['last4'],
            ]);
        } catch (InvalidRequest $e) {
            throw new PaymentFailedException;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getValidTestToken(string $cardNumber = self::TEST_CARD): string
    {
        return Token::create([
            "card" => [
                "number" => $cardNumber,
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

        return $this->newChargesSince($latestCharge)->map(function ($stripeCharge) {
            return new Charge([
                'amount' => $stripeCharge['amount'],
                'card_last_four' => $stripeCharge['source']['last4'],
            ]);
        });
    }

    /**
     * @return StripeCharge
     */
    protected function lastCharge(): StripeCharge
    {
        return array_first(
            StripeCharge::all(
                ["limit" => 1],
                ['api_key' => $this->apiKey]
            )['data']
        );
    }

    /**
     * @param StripeCharge $lastCharge
     *
     * @return Collection
     */
    private function newChargesSince(StripeCharge $lastCharge): Collection
    {
        $newCharges = StripeCharge::all(
            [
                "ending_before" => $lastCharge->id,
            ],
            ['api_key' => $this->apiKey]
        )['data'];

        return collect($newCharges);
    }
}
