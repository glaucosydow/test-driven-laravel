<?php

namespace Tests\Feature;

use App\Concert;
use Carbon\Carbon;
use Tests\TestCase;
use App\Billing\PaymentGateway;
use App\Billing\FakePaymentGateway;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PurchaseTicketsTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @var PaymentGateway
     */
    protected $paymentGateway;

    protected function setUp()
    {
    	parent::setUp();

    	$this->paymentGateway = new FakePaymentGateway;
    	$this->app->instance(PaymentGateway::class, $this->paymentGateway);
    }

    /** @test */
    public function customer_can_purchase_concert_tickets()
    {
		// Arrange
		// Create a concert.
		$concert = factory(Concert::class)->create(['ticket_price' => 3250]);

		// Act
		// Purchase concert tickets.
		$response = $this->json('POST', "/concerts/{$concert->id}/orders", [
			'email' => 'john@example.com',
			'ticket_quantity' => 3,
			'payment_token' => $this->paymentGateway->getValidTestToken(),
		]);

		// Assert
		$response->assertStatus(201);

		// Make sure the customer was charged the correct amount.
		$this->assertEquals(9750, $this->paymentGateway->totalCharges());

		// Make sure that an order exists for this customer.
		$order = $concert->orders()->where('email', 'john@example.com')->first();
		$this->assertNotNull($order);
		$this->assertCount(3, $order->tickets);
    }

    /** @test */
    public function email_is_required_to_purchase_tickets()
    {
        $concert = factory(Concert::class)->create();

		$response = $this->json('POST', "/concerts/{$concert->id}/orders", [
			'ticket_quantity' => 3,
			'payment_token' => $this->paymentGateway->getValidTestToken(),
		]);

		$response->assertStatus(422);
		$jsonResponse = json_decode($response->getContent(), true);
		$this->assertArrayHasKey('email', $jsonResponse);
    }

    /** @test */
    public function email_needs_to_be_valid()
    {
        $concert = factory(Concert::class)->create();

		$response = $this->json('POST', "/concerts/{$concert->id}/orders", [
			'email' => 'justme',
			'ticket_quantity' => 3,
			'payment_token' => $this->paymentGateway->getValidTestToken(),
		]);

		$response->assertStatus(422);
		$jsonResponse = json_decode($response->getContent(), true);
		$this->assertArrayHasKey('email', $jsonResponse);
    }

    /** @test */
    public function ticket_quantity_is_required_to_make_a_purchase()
    {
        $concert = factory(Concert::class)->create();

		$response = $this->json('POST', "/concerts/{$concert->id}/orders", [
			'email' => 'john@example.com',
			'payment_token' => $this->paymentGateway->getValidTestToken(),
		]);

		$response->assertStatus(422);
		$jsonResponse = json_decode($response->getContent(), true);
		$this->assertArrayHasKey('ticket_quantity', $jsonResponse);
    }

    /** @test */
    public function ticket_quantity_must_be_an_integer_to_make_a_purchase()
    {
        $concert = factory(Concert::class)->create();

		$response = $this->json('POST', "/concerts/{$concert->id}/orders", [
			'email' => 'john@example.com',
			'ticket_quantity' => 'aaa',
			'payment_token' => $this->paymentGateway->getValidTestToken(),
		]);

		$response->assertStatus(422);
		$jsonResponse = json_decode($response->getContent(), true);
		$this->assertArrayHasKey('ticket_quantity', $jsonResponse);
    }

    /** @test */
    public function ticket_quantity_must_be_greater_than_zero_to_make_a_purchase()
    {
        $concert = factory(Concert::class)->create();

		$response = $this->json('POST', "/concerts/{$concert->id}/orders", [
			'email' => 'john@example.com',
			'ticket_quantity' => 0,
			'payment_token' => $this->paymentGateway->getValidTestToken(),
		]);

		$response->assertStatus(422);
		$jsonResponse = json_decode($response->getContent(), true);
		$this->assertArrayHasKey('ticket_quantity', $jsonResponse);
    }

    /** @test */
    public function payment_token_is_required_to_make_a_purchase()
    {
        $concert = factory(Concert::class)->create();

		$response = $this->json('POST', "/concerts/{$concert->id}/orders", [
			'email' => 'john@example.com',
			'ticket_quantity' => 3,
		]);

		$response->assertStatus(422);
		$jsonResponse = json_decode($response->getContent(), true);
		$this->assertArrayHasKey('payment_token', $jsonResponse);
    }
}
