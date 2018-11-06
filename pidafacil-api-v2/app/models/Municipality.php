<?php

class Municipality extends Eloquent {
	
	protected $table = 'com_municipalities';
	protected $primaryKey = 'municipality_id';

	
    
    public function zones()
    {
        return $this->hasMany('Zone','municipality_id','municipality_id');
    }

}