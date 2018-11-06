<?php

class ConditionOption extends \Eloquent {
	protected $table = 'res_product_conditions_options';
	protected $primaryKey = 'condition_option_id';

	public function restaurant() {
		return $this->belongsToMany('Restaurant', 'res_restaurant_conditions_options', 'condition_option_id', 'restaurant_id')->withTimestamps();
	
	}

	public function condition() {
		return $this->belongsTo('Condition', 'condition_id', 'condition_id');
	}
}