<?php

namespace Tests\Unit;

use App\Concert;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use DatabaseMigrations;

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
