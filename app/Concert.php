<?php

namespace App;

use App\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

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

    public function orderTickets($email, $ticketQuantity)
    {
        $order = $this->orders()->create(['email' => $email]);

        foreach (range(1, $ticketQuantity) as $i) {
            $order->tickets()->create([]);
        }

        return $order;
    }
}
