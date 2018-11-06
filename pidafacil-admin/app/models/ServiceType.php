<?php

class ServiceType extends Eloquent {
	
	protected $table = 'res_service_types';
	protected $primaryKey = 'service_type_id';

	public function orders() {
		return $this->hasMany('Order', 'order_id', 'order_id');
	}

    public function restaurants() {
        return $this->belongsToMany('Restaurant', 'res_restaurants_service_types', 'service_type_id', 'restaurant_id');
    }
}