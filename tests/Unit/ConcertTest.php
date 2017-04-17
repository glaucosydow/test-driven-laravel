<?php

namespace Tests\Unit;

use App\Concert;
use Carbon\Carbon;
use Tests\TestCase;

class ConcertTest extends TestCase
{
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
}