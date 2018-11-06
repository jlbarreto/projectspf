<?php

class Tag extends Eloquent {
	
	protected $table = 'com_tags';
	protected $primaryKey = 'tag_id';

	public function tagType() {
		return $this->hasOne('TagType', 'tag_type_id', 'tag_type_id');
	} 

	public function restaurants()
    {
		return $this->belongsToMany('Restaurant', 'res_tags', 'tag_id', 'restaurant_id');
	}
}