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
        ]);
        // Create some tickets.
        $ticket = factory(Ticket::class)->create([
            'order_id' => $order->id,
            'concert_id' => $concert->id,
        ]);

        // Visit the order confirmation page.
        $response = $this->get('/order/CONFIRMATIONNUMBER1234');

        $this->throwExceptionIfInResponse($response);

        // Assert we see the correct order details.
        $response->assertStatus(200);

        $response->assertViewHas('order', function ($viewOrder) use ($order) {
            return $viewOrder->id === $order->id;
        });
    }
}
