<?php

namespace Tests\Browser;

use App\Concert;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ViewConcertListingTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function user_can_view_a_published_concert_listing()
    {
        $concert = factory(Concert::class)->states('published')->create([
            'title' => 'Red Cord',
            'subtitle' => 'with Animosity and Lethargy',
            'date' => Carbon::parse('December 13, 2016 8:00pm'),
            'ticket_price' => 3250, // stored in cents
            'venue' => 'The Mosh Pit',
            'venue_address' => '123 Example Lane',
            'city' => 'Laraville',
            'state' => 'ON',
            'zip' => '17916',
            'additional_information' => 'For tickets, call (555) 555-5555.',
        ]);

        $this->browse(function ($browser) use ($concert) {
            $browser->visit('/concerts/' . $concert->id)
                // ->pause(5000)
                ->assertSee('Red Cord')
                ->assertSee('with Animosity and Lethargy')
                ->assertSee('December 13, 2016')
                ->assertSee('8:00pm')
                ->assertSee('32.50')
                ->assertSee('The Mosh Pit')
                ->assertSee('123 Example Lane')
                ->assertSee('Laraville, ON 17916')
                ->assertSee('For tickets, call (555) 555-5555.')
            ;
        });
    }

    /** @test */
    public function user_cannot_view_unpublished_concert_listings()
    {
        $concert = factory(Concert::class)->states('unpublished')->create();

        $this->browse(function ($browser) use ($concert) {
            $browser->visit('/concerts/' . $concert->id)
                ->assertSee('NotFoundException');
        });
    }
}
