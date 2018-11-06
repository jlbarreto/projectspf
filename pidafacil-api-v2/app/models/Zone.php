<?php

class Zone extends Eloquent {
	
	protected $table = 'com_zones';
	protected $primaryKey = 'zone_id';

    public function restaurants()
    {
        return $this->belongsToMany('Zone','restaurants_zones')->withPivot('shipping_charge');
    }
}