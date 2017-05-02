<?php

namespace Tests\Unit;

use App\Billing\NotEnoughTicketsException;
use App\Concert;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ConcertTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function can_get_formatted_date()
    {
        $concert = factory(Concert::class)->make([
            'date' => Carbon::parse('2012-01-16 8pm'),
        ]);

        $this->assertEquals('January 16, 2012', $concert->formatted_date);
    }

    /** @test */
    public function can_get_formatted_time()
    {
        $concert = factory(Concert::class)->make([
            'date' => Carbon::parse('2013-12-16 1pm'),
        ]);

        $this->assertEquals('1:00pm', $concert->formatted_time);
    }

    /** @test */
    public function can_get_ticket_price_in_dollars()
    {
        $concert = factory(Concert::class)->make([
            'ticket_price' => 5050,
        ]);

        $this->assertEquals('50.50', $concert->ticket_price_in_dollars);
    }

    /** @test */
    public function concerts_with_a_published_at_date_are_published()
    {
        $publishedConcertA = factory(Concert::class)->create(['published_at' => Carbon::parse('-1 week')]);
        $publishedConcertB = factory(Concert::class)->create(['published_at' => Carbon::parse('-1 week')]);
        $unpublishedConcert = factory(Concert::class)->create(['published_at' => null]);

        $publishedConcerts = Concert::published()->get();

        $this->assertTrue($publishedConcerts->contains($publishedConcertA));
        $this->assertTrue($publishedConcerts->contains($publishedConcertB));
        $this->assertFalse($publishedConcerts->contains($unpublishedConcert));
    }

    /** @test */
    public function can_order_concert_tickets()
    {
        $concert = factory(Concert::class)->create();
        $concert->addTickets(5);

        $order = $concert->orderTickets('jane@example.com', 3);

        $this->assertEquals('jane@example.com', $order->email);
        $this->assertCount(3, $order->tickets);
        $this->assertEquals(2, $concert->ticketsRemaining());
    }

    /** @test */
    public function can_add_tickets()
    {
        $concert = factory(Concert::class)->create();
        $concert->addTickets(50);

        $this->assertEquals(50, $concert->ticketsRemaining());
    }

    /** @test */
    public function tickets_remaining_does_not_include_tickets_associated_with_an_order()
    {
        $concert = factory(Concert::class)->create();
        $concert->addTickets(50);

        $concert->orderTickets('jane@example.com', 30);

        $this->assertEquals(20, $concert->ticketsRemaining());
    }

    /** @test */
    public function trying_to_purchase_more_tickets_than_remain_throws_an_exception()
    {
        $concert = factory(Concert::class)->create();
        $concert->addTickets(10);

        try {
            $concert->orderTickets('jane@example.com', 11);
        } catch (NotEnoughTicketsException $e) {
            $this->assertFalse($concert->hasOrdersFor('jane@example.com'));
            $this->assertEquals(10, $concert->ticketsRemaining());

            return;
        }

        $this->fail("Order succeeded even though there weren't enough tickets remaining.");
    }

    /** @test */
    public function cannot_order_tickets_that_have_been_already_purchased()
    {
        $concert = factory(Concert::class)->create();
        $concert->addTickets(10);

        $concert->orderTickets('jane@example.com', 3);

        try {
            $concert->orderTickets('jerry@example.com', 8);
        } catch (NotEnoughTicketsException $e) {
            $this->assertFalse($concert->hasOrdersFor('jerry@example.com'));
            $this->assertEquals(7, $concert->ticketsRemaining());

            return;
        }

        $this->fail("Order succeeded even though the number of tickets order exceeded the one remaining.");
    }

    /** @test */
    public function can_reserve_available_tickets()
    {
        $concert = factory(Concert::class)->create();
        $concert->addTickets(3);
        $this->assertEquals(3, $concert->ticketsRemaining());

        $reservation = $concert->reserveTickets(2, 'john@example.com');

        $this->assertCount(2, $reservation->tickets());
        $this->assertEquals('john@example.com', $reservation->email());
        $this->assertEquals(1, $concert->ticketsRemaining());
    }

    /** @test */
    public function cannot_reserve_tickets_that_have_already_been_purchased()
    {
        $concert = factory(Concert::class)->create();
        $concert->addTickets(3);

        $concert->orderTickets('jane@example.com', 2);

        try {
            $concert->reserveTickets(2, 'john@example.com');
        } catch (NotEnoughTicketsException $e) {
            $this->assertEquals(1, $concert->ticketsRemaining());

            return;
        }

        $this->fail("Reserving tickets succeeded even though the tickets were already sold.");
    }

    /** @test */
    public function cannot_reserve_tickets_that_have_already_been_reseved()
    {
        $concert = factory(Concert::class)->create();
        $concert->addTickets(3);

        $concert->reserveTickets(2, 'john@example.com');

        try {
            $concert->reserveTickets(2, 'jane@example.com');
        } catch (NotEnoughTicketsException $e) {
            $this->assertEquals(1, $concert->ticketsRemaining());

            return;
        }

        $this->fail("Reserving tickets succeeded even though the tickets were already reserved.");
    }
}
