<?php

class OrderStatusLog extends \Eloquent {
	
	protected $table = 'req_order_status_logs';
	protected $primaryKey = 'status_log_id';

	public function order() {
		return $this->belongsTo('Order', 'order_id', 'order_id');
	}

	public function orderStatus() {
		return $this->belongsTo('OrderStatus', 'order_status_id', 'order__status_id');
	}

	public function user() {
		return $this->belongsTo('User', 'user_id', 'user_id');
	}

}