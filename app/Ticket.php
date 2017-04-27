<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

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
}
