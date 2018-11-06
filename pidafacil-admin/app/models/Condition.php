<?php

class Condition extends Eloquent {
	
	protected $table = 'res_conditions';
	protected $primaryKey = 'condition_id';

    public static $reglas = array(
        'condition' => 'required|min:5'
        );

    public static $presentacion = array(
        'condition' => 'condición'
        );

    public static function validacion($data){
        //Se realiza la validación especificando las reglas arriba incluidas
        $validacion = Validator::make($data, static::$reglas);
        //Se proporcionan los nombres  los campos existentes
        $validacion->setAttributeNames(static::$presentacion); 

        return $validacion;     
    }


	public function products()
    {
        return $this->belongsToMany('Product','res_products_conditions', 'condition_id', 'product_id');
    }

    public function opciones()
    {
        return $this->hasMany('ConditionOption','condition_id','condition_id');
    }

}