<?php

class Motorista extends Eloquent {
	
	protected $table = 'motoristas';
	protected $primaryKey = 'motorista_id';

	public function orders() {
		return $this->belongsToMany('Order', 'req_order_motorista', 'motorista_id', 'order_id');
	}

}