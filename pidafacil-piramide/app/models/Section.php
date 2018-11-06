<?php

class Section extends \Eloquent {

	protected $table = 'res_sections';
	protected $primaryKey = 'section_id';


	public static $reglas = array(
		'section' 			=> 'required|min:5', 
		'section_order_id'	=> 'integer'
		);

    public static $presentacion = array(
		'section' 			=> 'sección', 
		'section_order_id'	=> 'orden de sección'
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
    	return $this->belongsTo('Restaurant','restaurant_id','restaurant_id');

    }

    public function products()
    {
    	return $this->hasMany('Product','section_id','section_id');
    }
}