<?php

namespace Tests\Browser;

use App\Concert;
use Carbon\Carbon;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ExampleTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function user_can_view_a_concert_listing()
    {
        // Arrange
        // Create a concert
        $concert = Concert::create([
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

        // Act
        // View the concert listing
        $this->browse(function ($browser) use ($concert) {
            $browser->visit('/concerts/' . $concert->id)
                // ->pause(5000)
            ;


            // Assert
            // See the concert details
            $browser->assertSee('Red Cord')
                ->assertSee('with Animosity and Lethargy')
                ->assertSee('December 13, 2016')
                ->assertSee('8:00pm')
                ->assertSee('32.50')
                ->assertSee('The Mosh Pit')
                ->assertSee('123 Example Lane')
                ->assertSee('Laraville, ON 17916')
                ->assertSee('For tickets, call (555) 555-5555.');
        });
    }
}

