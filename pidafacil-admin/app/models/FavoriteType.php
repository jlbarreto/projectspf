<?php

class FavoriteType extends \Eloquent {

	protected $table = 'com_facorite_types';
	protected $primaryKey = 'favorite_type_id';

	public function users() {
		return $this->belongsToMany('User', 'diner_favorites', 'favorite_type_id', 'user_id');
	}

}