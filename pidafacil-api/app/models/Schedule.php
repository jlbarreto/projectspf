<?php

class Schedule extends \Eloquent {

	protected $table = 'res_schedules';
	protected $primaryKey = 'schedule_id';
	
	public function restaurant()
    {
    	return $this->belongsTo('Restaurant','restaurant_id','restaurant_id');

    }


}