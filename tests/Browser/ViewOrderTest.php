<?php

namespace Tests\Browser;

use App\Concert;
use App\Order;
use App\Ticket;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ViewOrderTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function user_can_view_their_order_confirmation()
    {
        // Create a concert.
        $concert = factory(Concert::class)->create();
        // Create an order.
        $order = factory(Order::class)->create([
            'confirmation_number' => 'CONFIRMATIONNUMBER1234',
            'amount' => 8500,
            'card_last_four' => '1881',
        ]);
        // Create some tickets.
        $ticketA = factory(Ticket::class)->create([
            'order_id' => $order->id,
            'concert_id' => $concert->id,
            'code' => 'TICKET123',
        ]);
        $ticketB = factory(Ticket::class)->create([
            'order_id' => $order->id,
            'concert_id' => $concert->id,
            'code' => 'TICKET456',
        ]);

        // Visit the order confirmation page.
        $response = $this->get('/order/CONFIRMATIONNUMBER1234');

        $this->throwExceptionIfInResponse($response);

        // Assert we see the correct order details.
        $response->assertStatus(200);

        $response->assertViewHas('order', function ($viewOrder) use ($order) {
            return $viewOrder->id === $order->id;
        });
        $response->assertSee('CONFIRMATIONNUMBER1234');
        $response->assertSee('$85.00');
        $response->assertSee('**** **** **** 1881');
        $response->assertSee('TICKET123');
        $response->assertSee('TICKET456');
    }
}
