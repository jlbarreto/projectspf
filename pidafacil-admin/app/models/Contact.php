<?php

class Contact extends Eloquent {
	
	protected $table = 'res_contacts';
	protected $primaryKey = 'contact_id';


	public function restaurants() {
		return $this->belongsToMany('Restaurant', 'res_restaurants_contacts', 'contact_id', 'restaurant_id');
	}
}