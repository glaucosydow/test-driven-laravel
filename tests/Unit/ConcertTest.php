<?php

namespace Tests\Unit;

use App\Concert;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

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

	    $order = $concert->orderTickets('jane@example.com', 3);

	    $this->assertEquals('jane@example.com', $order->email);
	    $this->assertCount(3, $order->tickets);
	}
}