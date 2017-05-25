<?php

namespace Tests\Unit;

use App\RandomOrderConfirmationNumberGenerator;
use Tests\TestCase;

class RandomOrderConfirmationNumberGeneratorTest extends TestCase
{
    // Can only contain uppercase letters and numbers: ABCDEFGHJKLMNPQRSTUVXYZ23456789
    // Cannot contain ambigous characters: I and 1, O and 0
    // Must be 24 characters long.
    // All order confirmation numbers must be unique.

    /** @test */
    public function must_be_24_characters_long()
    {
        $generator = new RandomOrderConfirmationNumberGenerator;

        $confirmationNumber = $generator->generate();

        $this->assertEquals(24, \strlen($confirmationNumber));
    }

    /** @test */
    public function can_contain_only_uppercase_letters_and_numbers()
    {
        $generator = new RandomOrderConfirmationNumberGenerator;

        $confirmationNumber = $generator->generate();

        $this->assertRegExp('/^[A-Z0-9]+$/', $confirmationNumber);
    }

    /** @test */
    public function cannot_contain_ambigous_characters()
    {
        $generator = new RandomOrderConfirmationNumberGenerator;

        $confirmationNumber = $generator->generate();

        // $this->assertNotRegExp('/^[I10O]+$/', $confirmationNumber);
        $this->assertFalse(\stripos($confirmationNumber, '1'));
        $this->assertFalse(\stripos($confirmationNumber, 'I'));
        $this->assertFalse(\stripos($confirmationNumber, '0'));
        $this->assertFalse(\stripos($confirmationNumber, 'O'));
    }

    /** @test */
    public function confirmation_number_must_be_unique()
    {
        $generator = new RandomOrderConfirmationNumberGenerator;

        $confirmationNumbers = \array_map(function () use ($generator) {
            return $generator->generate();
        }, \range(1, 100));

        $this->assertCount(100, \array_unique($confirmationNumbers));
    }
}
