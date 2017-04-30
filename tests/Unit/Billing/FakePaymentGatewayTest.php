<?php

namespace Tests\Unit;

use App\Billing\FakePaymentGateway;
use Tests\TestCase;

class FakePaymentGatewayTest extends TestCase
{
    /** @test */
    public function changes_with_a_valid_payment_token_are_successful()
    {
        $paymentGateway = new FakePaymentGateway;

        $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());

        $this->assertEquals(2500, $paymentGateway->totalCharges());
    }

    /**
     * @test
     * @expectedException \App\Billing\PaymentFailedException
     */
    public function charges_with_an_invalid_payment_token_fail()
    {
        $paymentGateway = new FakePaymentGateway;

        $paymentGateway->charge(2500, 'invalid-payment-token');
    }

    /** @test */
    public function charge_back_is_successfull()
    {
        $paymentGateway = new FakePaymentGateway;

        $paymentGateway->charge(2600, $paymentGateway->getValidTestToken());
        $this->assertEquals(2600, $paymentGateway->totalCharges());

        $paymentGateway->chargeBack(1300, $paymentGateway->getValidTestToken());
        $this->assertEquals(1300, $paymentGateway->totalCharges());
    }

    /**
     * @test
     * @expectedException \App\Billing\PaymentFailedException
     */
    public function charge_back_with_an_invalid_payment_token_fail()
    {
        $paymentGateway = new FakePaymentGateway;

        $paymentGateway->chargeBack(2500, 'invalid-payment-token');
    }

    /** @test */
    public function charge_back_doesnt_happen_if_there_arent_any_charges()
    {
        $paymentGateway = new FakePaymentGateway;

        $paymentGateway->chargeBack(2500, $paymentGateway->getValidTestToken());

        $this->assertEquals(0, $paymentGateway->totalCharges());
    }

    /** @test */
    public function running_a_hook_before_the_first_charge()
    {
        $paymentGateway = new FakePaymentGateway;
        $called = false;

        $paymentGateway->beforeFirstCharge(function ($paymentGateway) use (&$called) {
            $called = true;
            $this->assertEquals(0, $paymentGateway->totalCharges());
        });

        $paymentGateway->charge(2600, $paymentGateway->getValidTestToken());

        $this->assertEquals(2600, $paymentGateway->totalCharges());
        $this->assertTrue($called);
    }
}
