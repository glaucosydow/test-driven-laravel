<?php

namespace App;

use App\Concert;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    protected $guarded = [];

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeAvailable(Builder $query)
    {
        return $query->whereNull('order_id')->whereNull('reserved_at');
    }

    /**
     * Release a reserved ticket.
     */
    public function release()
    {
        $this->update(['reserved_at' => null]);
    }

    /**
     * @return BelongsTo
     */
    public function concert(): BelongsTo
    {
    	return $this->belongsTo(Concert::class);
    }

    /**
     * @return int
     */
    public function getPriceAttribute(): int
    {
    	return $this->concert->ticket_price;
    }

    /**
     * Reserve a ticket.
     */
    public function reserve()
    {
        $this->update(['reserved_at' => Carbon::now()]);
    }
}
