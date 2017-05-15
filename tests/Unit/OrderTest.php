<?php

namespace Tests\Unit;

use App\Concert;
use App\Order;
use App\Reservation;
use App\Ticket;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function create_an_order_from_email_and_tickets()
    {
        $concert = factory(Concert::class)->create();
        $concert->addTickets(5);
        $this->assertEquals(5, $concert->ticketsRemaining());

        $order = Order::forTickets($concert->findTickets(3), 'john@example.com', 3600);

        $this->assertEquals('john@example.com', $order->email);
        $this->assertEquals(3, $order->ticketQuantity());
        $this->assertEquals(3600, $order->amount);
        $this->assertEquals(2, $concert->ticketsRemaining());
    }

    /** @test */
    public function retrieving_an_order_by_confirmation_number()
    {
        $order = factory(Order::class)->create([
            'confirmation_number' => 'CONFIRMATIONNUMBER1234',
        ]);

        $foundOrder = Order::findByConfirmationNumber('CONFIRMATIONNUMBER1234');

        $this->assertEquals($order->id, $foundOrder->id);
    }

    /** @test */
    public function retrieving_a_nonexistent_order_by_confirmation_number_throws_an_exception()
    {
        try {
            Order::findByConfirmationNumber('abcd');
        } catch (ModelNotFoundException $e) {
            return;
        }

        $this->fail("Order found even though the confirmation number doesn't exist.");
    }

    /** @test */
    public function convert_to_an_array()
    {
        $concert = factory(Concert::class)->create(['ticket_price' => 1200]);
        $concert->addTickets(5);
        $order = $concert->orderTickets('john@example.com', 5);

        $result = $order->toArray();

        $this->assertEquals([
            'email' => 'john@example.com',
            'ticket_quantity' => 5,
            'amount' => 6000,
        ], $result);
    }
}
