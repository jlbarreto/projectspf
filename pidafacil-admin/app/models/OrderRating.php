<?php

class OrderRating extends Eloquent {
	
	protected $table = 'req_order_ratings';
	protected $primaryKey = 'order_rating_id';

	public $timestamps = false;

	public function order() {
		return $this->belongsTo('Order', 'order_id', 'order_id');
	}

	public function user() {
		return $this->belongsTo('User', 'user_id', 'user_id');
	}

}