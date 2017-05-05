<?php

namespace Tests\Unit;

use App\Billing\FakePaymentGateway;
use Tests\TestCase;

class FakePaymentGatewayTest extends TestCase
{
    /** @test */
    public function can_fetch_charges_created_during_a_callback()
    {
        $paymentGateway = $this->getPaymentGateway();
        $paymentGateway->charge(2000, $paymentGateway->getValidTestToken());
        $paymentGateway->charge(3000, $paymentGateway->getValidTestToken());

        $newCharges = $paymentGateway->newChargesDuring(function ($paymentGateway) {
            $paymentGateway->charge(4000, $paymentGateway->getValidTestToken());
            $paymentGateway->charge(5000, $paymentGateway->getValidTestToken());
        });

        $this->assertCount(2, $newCharges);
        $this->assertEquals([4000, 5000], $newCharges->all());
    }

    /** @test */
    public function charges_with_a_valid_payment_token_are_successful()
    {
        $paymentGateway = $this->getPaymentGateway();
        $paymentGateway->charge(2000, $paymentGateway->getValidTestToken());

        $newCharges = $paymentGateway->newChargesDuring(function ($paymentGateway) {
            $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());
        });

        // Verify that the charge was completed successfully.
        $this->assertCount(1, $newCharges);
        $this->assertEquals(2500, $newCharges->sum());
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
        $timesCalled = 0;

        $paymentGateway->beforeFirstCharge(function ($paymentGateway) use (&$timesCalled) {
            $timesCalled++;
            $paymentGateway->charge(2600, $paymentGateway->getValidTestToken());
            $this->assertEquals(2600, $paymentGateway->totalCharges());
        });

        $paymentGateway->charge(2600, $paymentGateway->getValidTestToken());

        $this->assertEquals(5200, $paymentGateway->totalCharges());
        $this->assertEquals(1, $timesCalled);
    }

    protected function getPaymentGateway()
    {
        return new FakePaymentGateway;
    }
}
