<?php

class Ingredient extends \Eloquent {
	protected $table = 'res_ingredients';
	protected $primaryKey = 'ingredient_id';

	public static $reglas = array(
		'ingredient' 		=> 'required|min:2',
                'restaurant_id'         => 'required'
		);

    public static $presentacion = array(
		'ingredient' 		=> 'ingrediente'
		);

    public static function validacion($data){
		//Se realiza la validación especificando las reglas arriba incluidas
		$validacion = Validator::make($data, static::$reglas);
		//Se proporcionan los nombres  los campos existentes
		$validacion->setAttributeNames(static::$presentacion); 

		return $validacion;		
	}

    public function restaurant()
    {
        return $this->belongsTo('Restaurant', 'restaurant_id', 'restaurant_id');
    }

    public function product()
    {
        return $this->belongsToMany('Product','res_products_ingredients')->withPivot('removable')->withTimestamps();
    }



}