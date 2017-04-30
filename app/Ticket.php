<?php

namespace App;

use App\Concert;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeAvailable(Builder $query)
    {
        return $query->whereNull('order_id');
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
}
