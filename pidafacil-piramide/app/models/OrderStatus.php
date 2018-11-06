<?php

class OrderStatus extends Eloquent {
	
	protected $table = 'req_order_status';
	protected $primaryKey = 'order_status_id';

	public $timestamps = false;

}