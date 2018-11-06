<?php

class Country extends Eloquent {
	
	protected $table = 'com_countries';
	protected $primaryKey = 'country_id';

	public function users() {
		reutrn $this->hasMany('User', 'user_id', 'user_id');
	}

}