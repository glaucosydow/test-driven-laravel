<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Billing\FakePaymentGateway;

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
}