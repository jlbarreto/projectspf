<?php

class Ingredient extends \Eloquent {
	protected $table = 'res_ingredients';
	protected $primaryKey = 'ingredient_id';

    public function restaurant()
    {
        return $this->belongsTo('Restaurant', 'restaurant_id', 'restaurant_id');
    }

    public function product()
    {
        return $this->belongsToMany('Product','res_products_ingredients')->withPivot('removable')->withTimestamps();
    }
}