<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;

class Reservation
{
	/**
	 * @var Collection
	 */
	protected $tickets;

	public function __construct(Collection $tickets)
	{
		$this->tickets = $tickets;
	}

	/**
	 * @return int
	 */
	public function totalCost()
	{
		return $this->tickets->sum('price');
	}

	/**
	 * Cancel a reservation.
	 */
	public function cancel()
	{
		foreach ($this->tickets as $ticket) {
			$ticket->release();
		}
	}

	/**
	 * @return Collection
	 */
	public function tickets(): Collection
	{
		return $this->tickets;
	}
}