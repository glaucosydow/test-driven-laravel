<?php

namespace App\Billing;

use Stripe\Charge;

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
        Charge::create(
            [
                "amount" => $amount,
                "currency" => "usd",
                "source" => $token, // obtained with Stripe.js
            ],
            ['api_key' => $this->apiKey]
        );
    }
}
