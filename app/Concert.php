<?php

namespace App;

use App\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Billing\NotEnoughTicketsException;

class Concert extends Model
{
    protected $guarded = [];

    protected $dates = ['date'];

    /**
     * @return string
     */
    public function getFormattedDateAttribute()
    {
    	return $this->date->format('F j, Y');
    }

    /**
     * @return string
     */
    public function getFormattedTimeAttribute()
    {
    	return $this->date->format('g:ia');
    }

    /**
     * @return string
     */
    public function getTicketPriceInDollarsAttribute()
    {
    	return number_format($this->ticket_price / 100, 2);
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopePublished(Builder $query)
    {
    	return $query->whereNotNull('published_at');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * @param string $email
     * @param int $ticketQuantity
     *
     * @return Order
     */
    public function orderTickets(string $email, int $ticketQuantity): Order
    {
        $tickets = $this->tickets()->available()->take($ticketQuantity)->get();

        if ($tickets->count() < $ticketQuantity) {
            throw new NotEnoughTicketsException;
        }

        $order = $this->orders()->create(['email' => $email]);
        foreach ($tickets as $ticket) {
            $order->tickets()->save($ticket);
        }

        return $order;
    }

    /**
     * @param int $quantity
     */
    public function addTickets(int $quantity)
    {
        foreach (range(1, $quantity) as $i) {
            $this->tickets()->create([]);
        }
    }

    /**
     * @return int
     */
    public function ticketsRemaining(): int
    {
        return $this->tickets()->available()->count();
    }
}
