<?php

namespace Tests\Unit;

use Mockery;
use App\Concert;
use Tests\TestCase;
use App\Reservation;
use Illuminate\Database\Eloquent\Collection;

class ReservationTest extends TestCase
{
    /** @test */
    public function calculating_the_total_cost()
    {
        $tickets = new Collection([
        	(object) ['price' => 1200],
        	(object) ['price' => 1200],
        	(object) ['price' => 1200],
    	]);

        $reservation = new Reservation($tickets);

        $this->assertEquals(3600, $reservation->totalCost());
    }

    /** @test */
    public function retrieving_reserved_tickets()
    {
        $tickets = new Collection([
            (object) ['price' => 1200],
            (object) ['price' => 1200],
            (object) ['price' => 1200],
        ]);

        $reservation = new Reservation($tickets);

        $this->assertEquals($tickets, $reservation->tickets());
    }

    /** @test */
    public function reserved_tickets_are_released_when_a_reservation_is_cancelled()
    {
        $tickets = new Collection([
            Mockery::spy(Ticket::class),
            Mockery::spy(Ticket::class),
            Mockery::spy(Ticket::class),
        ]);

        $reservation = new Reservation($tickets);
        $reservation->cancel();

        foreach ($tickets as $ticket) {
            $ticket->shouldHaveReceived('release');
        }
    }
}
