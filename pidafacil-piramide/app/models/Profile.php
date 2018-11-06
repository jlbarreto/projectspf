<?php

class Profile extends Eloquent {
	protected $table = 'com_profiles';
	protected $primaryKey = 'profile_id';

    public function user()
    {
        return $this->belongsTo('User', 'user_id', 'user_id');
    }
}