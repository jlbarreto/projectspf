<?php

class OrderDetailProductCondition extends Eloquent {
	
	protected $table = 'req_product_conditions_options';
	protected $primaryKey = 'product_condition_option_id';

	public function condition() {
		return $this->belongsTo('Condition', 'product_condition_option_id', 'condition_id');
	}

	public function option() {
		return $this->belongsTo('ConditionOption', 'product_condition_option_id', 'condition_option_id');
	}

	public function orderDetail() {
		return $this->belongsTo('OrderDetail', 'order_det_id', 'order_det_id');
	}
}