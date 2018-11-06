<?php

class WebContent extends \Eloquent {
	
	protected $table = 'res_web_content';
	protected $primaryKey = 'landing_page_id';

	public function restaurant()
    {
        return $this->belongsToMany('Restaurant' ,'landing_page_id','landing_page_id');
    }
}