<?php

namespace Tests\Unit;

use App\Concert;
use Tests\TestCase;
use App\Reservation;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ReservationTest extends TestCase
{
	use DatabaseMigrations;

    /** @test */
    public function calculating_the_total_cost()
    {
        $concert = factory(Concert::class)->create(['ticket_price' => 1200]);
        $concert->addTickets(3);
        $tickets = $concert->findTickets(3);

        $reservation = new Reservation($tickets);

        $this->assertEquals(3600, $reservation->totalCost());
    }
}
