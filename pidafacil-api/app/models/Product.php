<?php

class Product extends \Eloquent {
    
	protected $table = 'res_products';
	protected $primaryKey = 'product_id';

	public function section()
	{
		return $this->belongsTo('Section','section_id','section_id');
	}
	
    public function restaurant()
    {
        return $this->hasManyThrough('Restaurant', 'Section','section_id','restaurant_id');
    }

    public function ingredients()
    {
        return $this->belongsToMany('Ingredient','res_products_ingredients')->withPivot('removable')->withTimestamps();
    }

    public function conditions()
    {
        return $this->belongsToMany('Condition','res_products_conditions','product_id','condition_id')->withTimestamps();
    }

    public function tags()
    {
        return $this->belongsToMany('Tag','res_product_tags','product_id','tag_id')->withTimestamps();
    }
}