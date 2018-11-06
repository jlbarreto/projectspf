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
    
    public function zone()
    {
        return $this->hasOne('Zone','zone_id','zone_id');
    }

	/*
	 * Validation
	 */
	public static $rules = array(
		'address_1' => 'required|min:1'
	);

    public static $display = array(
		'address_1' => 'Dirección'
	);
    
    public static function validation($data) {
		$validation = Validator::make($data, static::$rules);
		$validation->setAttributeNames(static::$display); 

		return $validation;		
	}
}