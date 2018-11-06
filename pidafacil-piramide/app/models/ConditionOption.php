<?php

class ConditionOption extends \Eloquent {
	protected $table = 'res_product_conditions_options';
	protected $primaryKey = 'condition_option_id';

    public static $reglas = array(
        'condition_option' => 'required|min:5'
        );

    public static $presentacion = array(
        'condition_option' => 'opción'
        );

    public static function validacion($data){
        //Se realiza la validación especificando las reglas arriba incluidas
        $validacion = Validator::make($data, static::$reglas);
        //Se proporcionan los nombres  los campos existentes
        $validacion->setAttributeNames(static::$presentacion); 

        return $validacion;     
    }

	public function restaurant() {
		return $this->belongsToMany('Restaurant', 'res_restaurant_conditions_options', 'condition_option_id', 'restaurant_id')->withTimestamps();
	
	}

	public function condition() {
		return $this->belongsTo('Condition', 'condition_id', 'condition_id');
	}

	public function orderDetails() {
		return $this->belongsToMany('OrderDetail', 'req_product_conditions_options', 'condition_option_id', 'order_det_id');
	}
}