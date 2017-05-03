<?php

namespace App\Billing;

use App\Billing\PaymentFailedException;
use Stripe\Charge;
use Stripe\Error\InvalidRequest;

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
}
