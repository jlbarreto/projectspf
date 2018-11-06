<?php

class PaymentMethod extends Eloquent {
	
	protected $table = 'res_payment_methods';
	protected $primaryKey = 'payment_method_id';

	public function orders() {

		return $this->hasMany('Order', 'order_id', 'order_id');

	}

	public function restaurant()
    {
        return $this->belongsToMany('Restaurant','res_restaurant_payment_methods')->withTimestamps();
    }
}