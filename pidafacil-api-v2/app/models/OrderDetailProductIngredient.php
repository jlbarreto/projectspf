<?php

class OrderDetailProductIngredient extends Eloquent {
	
	protected $table = 'req_product_ingredients';
	protected $primaryKey = 'product_ingredient_id';

	public function ingredient() {
		return $this->belongsTo('Ingredient', 'ingredient_id', 'ingredient_id');
	}

	public function orderDetail() {
		return $this->belongsTo('OrderDetail', 'order_det_id', 'order_det_id');
	}
}