<?php

namespace Tests\Unit;

use App\Ticket;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class TicketTest extends TestCase
{
	use DatabaseMigrations;

    /** @test */
    public function a_ticket_can_be_reserved()
    {
    	$ticket = factory(Ticket::class)->create();
    	$this->assertNull($ticket->reserved_at);

    	$ticket->reserve();

    	$this->assertNotNull($ticket->reserved_at);
    }
}
