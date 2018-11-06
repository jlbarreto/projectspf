<?php

class Condition extends \Eloquent {
	protected $table = 'res_conditions';
	protected $primaryKey = 'condition_id';

	public function products()
    {
        return $this->belongsToMany('Product','res_products_conditions', 'condition_id', 'product_id');
    }

    public function options()
    {
        return $this->hasMany('ConditionOption','condition_id','condition_id');
    }
}