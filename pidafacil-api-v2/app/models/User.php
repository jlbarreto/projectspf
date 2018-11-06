<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'com_users';

	protected $primaryKey = 'user_id';

	protected $fillable = array(
		'email', 
		'name', 
		'lastname', 
		'gender', 
		'birth_date', 
		'phone',
		'photo'
	);

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'status', 'remember_token');

	public function profiles() {
        return $this->hasMany('Profile', 'profile_id', 'profile_id');
    }

}
