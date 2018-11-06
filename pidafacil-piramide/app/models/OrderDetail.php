<?php

class OrderDetail extends Eloquent {
	
	protected $table = 'req_orders_det';
	protected $primaryKey = 'order_det_id';

	public function order() {
		return $this->belongsTo('Order', 'order_id', 'order_id');
	}

	public function getProduct() {
		return $this->belongsTo('Product', 'product_id', 'product_id');
	}

	public function conditions() {
		return $this->hasMany('OrderDetailProductCondition', 'order_det_id', 'order_det_id');
	}
	
	public function ingredients() {
		return $this->hasMany('OrderDetailProductIngredient', 'order_det_id', 'order_det_id');
	}
	/*
	public function conditions() {
		return $this->belongsToMany('OrderDetailProductCondition', 'req_product_conditions_options', 'order_det_id', 'condition_id')->withPivot('condition_option_id')->withTimestamps();;
	}

	public function ingredients() {
		return $this->belongsToMany('OrderDetailProductIngredient', 'req_product_ingredients', 'order_det_id', 'ingredient_id')->withPivot('remove')->withTimestamps();
	}
	*/

}