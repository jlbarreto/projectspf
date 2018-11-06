<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;
use Zizaco\Entrust\HasRole;

class User extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;
	use HasRole;

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

	public function country() {
		return $this->belongsTo('Country', 'country_id', 'country_id');
	}

	public function addresses() {
		return $this->hasMany('Address', 'address_id', 'address_id');
	}

	public function favoriteTypes() {
		return $this->belongsToMany('FavoriteType', 'diner_favorites', 'user_id', 'favorite_type_id');
	}

	public function tags() {
		return $this->belongsToMany('Tag', 'diner_tags', 'user_id', 'tag_id');
	}
    public function role() {
        return $this->belongsToMany('Role', 'assigned_roles', 'user_id', 'role_id');
    }
	public function restaurants() {
		return $this->belongsToMany('Restaurant', 'res_restaurants_users');
	}

	public function profiles() {
        return $this->hasMany('Profile', 'profile_id', 'profile_id');
    }

    public function orders() {
		return $this->belongsToMany('Order', 'req_order_ratings', 'user_id', 'order_id');
	}
}
