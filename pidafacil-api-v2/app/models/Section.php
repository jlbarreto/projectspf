<?php

class Section extends \Eloquent {
	
	protected $table = 'res_sections';
	protected $primaryKey = 'section_id';

	public function restaurant()
    {
    	return $this->belongsTo('Restaurant','restaurant_id','restaurant_id');
    }

    public function products()
    {
    	return $this->hasMany('Product','section_id','section_id');
    }
}