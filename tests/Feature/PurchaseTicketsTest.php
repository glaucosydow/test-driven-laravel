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
		$response = $this->orderTickets($concert, [
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
    public function an_order_is_not_created_if_payment_fails()
    {
        $this->disableExceptionHandling();

        $concert = factory(Concert::class)->create();

        $response = $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 1,
            'payment_token' => 'invalid-payment-token',
        ]);

        $response->assertStatus(422);
        $order = $concert->orders()->where('email', 'john@example.com')->first();
        $this->assertNull($order);
    }

    /** @test */
    public function email_is_required_to_purchase_tickets()
    {
        $concert = factory(Concert::class)->create();

		$response = $this->orderTickets($concert, [
			'ticket_quantity' => 3,
			'payment_token' => $this->paymentGateway->getValidTestToken(),
		]);

		$this->assertValidationError('email', $response);
    }

    /** @test */
    public function email_needs_to_be_valid()
    {
        $concert = factory(Concert::class)->create();

		$response = $this->orderTickets($concert, [
			'email' => 'justme',
			'ticket_quantity' => 3,
			'payment_token' => $this->paymentGateway->getValidTestToken(),
		]);

		$this->assertValidationError('email', $response);
    }

    /** @test */
    public function ticket_quantity_is_required_to_make_a_purchase()
    {
        $concert = factory(Concert::class)->create();

		$response = $this->orderTickets($concert, [
			'email' => 'john@example.com',
			'payment_token' => $this->paymentGateway->getValidTestToken(),
		]);

		$this->assertValidationError('ticket_quantity', $response);
    }

    /** @test */
    public function ticket_quantity_must_be_an_integer_to_make_a_purchase()
    {
        $concert = factory(Concert::class)->create();

		$response = $this->orderTickets($concert, [
			'email' => 'john@example.com',
			'ticket_quantity' => 'aaa',
			'payment_token' => $this->paymentGateway->getValidTestToken(),
		]);

		$this->assertValidationError('ticket_quantity', $response);
    }

    /** @test */
    public function ticket_quantity_must_be_greater_than_zero_to_make_a_purchase()
    {
        $concert = factory(Concert::class)->create();

		$response = $this->orderTickets($concert, [
			'email' => 'john@example.com',
			'ticket_quantity' => 0,
			'payment_token' => $this->paymentGateway->getValidTestToken(),
		]);

		$this->assertValidationError('ticket_quantity', $response);
    }

    /** @test */
    public function payment_token_is_required_to_make_a_purchase()
    {
        $concert = factory(Concert::class)->create();

		$response = $this->orderTickets($concert, [
			'email' => 'john@example.com',
			'ticket_quantity' => 3,
		]);

		$this->assertValidationError('payment_token', $response);
    }

    /**
     * Order tickets POST request.
     *
     * @param Concert $concert
     * @param array $params
     *
     * @return \Illuminate\Http\Response
     */
    protected function orderTickets($concert, array $params)
    {
		return $this->json('POST', "/concerts/{$concert->id}/orders", $params);
    }

    /**
     * @param string $key
     * @param \Illuminate\Http\Response $response
     */
    protected function assertValidationError($key, $response)
    {
    	$response->assertStatus(422);
    	$jsonResponse = json_decode($response->getContent(), true);
		$this->assertArrayHasKey($key, $jsonResponse);
    }
}
