<?php

class OrderDetail extends Eloquent {

	protected $table = 'req_orders_det';
	protected $primaryKey = 'order_det_id';

	public function order() {
		return $this->belongsTo('Order', 'order_id', 'order_id');
	}

	public function product() {
		return $this->belongsTo('Product', 'product_id', 'product_id');
	}

	public function conditions() {
		return $this->hasMany('OrderDetailProductCondition', 'order_det_id', 'order_det_id');
	}

	public function ingredients() {
		return $this->hasMany('OrderDetailProductIngredient', 'order_det_id', 'order_det_id')->where('remove', 0);
	}

}
