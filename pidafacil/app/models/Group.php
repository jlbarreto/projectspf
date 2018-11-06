<?php

class Group extends Eloquent {
	
	protected $table = 'sec_groups';
	protected $primaryKey = 'group_id';

	public function permissions() {
		return $this->belongsToMany('Permission', 'sec_groups_permissions', 'group_id', 'permission_id');
	}

	public function users() {
		return $this->belongsToMany('User', 'sec_users_groups', 'group_id', 'user_id');
	}
}