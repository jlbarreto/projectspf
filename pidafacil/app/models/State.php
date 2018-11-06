<?php

class State extends Eloquent {
	
	protected $table = 'com_states';
	protected $primaryKey = 'state_id';

	
    
    public function municipalities()
    {
        return $this->hasMany('Municipality','state_id','state_id');
    }

}