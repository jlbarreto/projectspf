<?php

class Order extends Eloquent {

	protected $table = 'req_orders';
	protected $primaryKey = 'order_id';

	public function restaurant() {
		return $this->belongsTo('Restaurant', 'restaurant_id', 'restaurant_id');
	}

	public function statusLogs() {
		return $this->hasMany('OrderStatusLog', 'order_id', 'order_id');
	}

	public function serviceType() {
		return $this->belongsTo('ServiceType', 'service_type_id', 'service_type_id');
	}

	public function paymentMethod() {
		return $this->belongsTo('PaymentMethod', 'payment_method_id', 'payment_method_id');
	}

	public function users() {
		return $this->belongsToMany('User', 'req_order_ratings', 'order_id', 'user_id');
	}

	public function products() {
		return $this->hasMany('OrderDetail', 'order_id', 'order_id');
	}

    public function motoristas() {
        return $this->belongsToMany('Motorista', 'req_order_motorista', 'order_id', 'motorista_id');
    }
}