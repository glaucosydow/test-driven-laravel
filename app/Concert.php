<?php

namespace App;

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
}
