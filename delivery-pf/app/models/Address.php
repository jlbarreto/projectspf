<?php

class Address extends Eloquent {
	
	protected $table = 'diner_addresses';
	protected $primaryKey = 'address_id';

	public function user() {
		return $this->belongsTo('User', 'user_id', 'user_id');
	}

	public function country() {
		return $this->belongsTo('Country', 'country_id', 'country_id');
	}

	/*
	 * Validation
	 */
	public static $rules = array(
		'address_name' 	=> 'required', 
		'address_1' 	=> 'required|min:6', 
		'city'			=> 'required',
		'state'			=> 'required'
	);

    public static $display = array(
		'address_name' 	=> 'Nombre de la direcci&oacute;n', 
		'address_1' 	=> 'Dirección', 
		'city'			=> 'Ciudad',
		'state'			=> 'Estado'
	);
    
    public static function validation($data) {
		$validation = Validator::make($data, static::$rules);
		$validation->setAttributeNames(static::$display); 

		return $validation;		
	}
}