<?php

namespace App;

use App\Billing\NotEnoughTicketsException;
use App\Order;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Concert extends Model
{
    protected $guarded = [];

    protected $dates = ['date'];

    /**
     * @return string
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->date->format('F j, Y');
    }

    /**
     * @return string
     */
    public function getFormattedTimeAttribute(): string
    {
        return $this->date->format('g:ia');
    }

    /**
     * @return string
     */
    public function getTicketPriceInDollarsAttribute(): string
    {
        return \number_format($this->ticket_price / 100, 2);
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at');
    }

    /**
     * @return HasMany
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Checks whether there are orders for
     * a given customer email.
     *
     * @param string $customerEmail
     *
     * @return bool
     */
    public function hasOrdersFor(string $customerEmail): bool
    {
        return $this->orders()->where('email', $customerEmail)->count() > 0;
    }

    /**
     * The number of orders for the given customer email.
     *
     * @param string $customerEmail
     *
     * @return Collection
     */
    public function ordersFor(string $customerEmail): Collection
    {
        return $this->orders()->where('email', $customerEmail)->get();
    }

    /**
     * @return HasMany
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * @param string $email
     * @param int    $ticketQuantity
     *
     * @return Order
     */
    public function orderTickets(string $email, int $ticketQuantity): Order
    {
        $tickets = $this->tickets()->available()->take($ticketQuantity)->get();

        if ($tickets->count() < $ticketQuantity) {
            throw new NotEnoughTicketsException;
        }

        $order = $this->orders()->create([
            'email' => $email,
            'amount' => $ticketQuantity * $this->ticket_price,
        ]);
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
        foreach (\range(1, $quantity) as $i) {
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
