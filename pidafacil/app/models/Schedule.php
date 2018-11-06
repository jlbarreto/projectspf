<?php

class Schedule extends \Eloquent {

	protected $table = 'res_schedules';
	protected $primaryKey = 'schedule_id';
	
	public static $reglas = array(
		'day_id'			=> 'required|integer|between:0,6',
		'opening_time' 		=> 'required|time',
		'closing_time'		=> 'required|time',
		'service_type_id'	=> 'required|exists:res_service_types,service_type_id'
		);

	public static $presentacion = array(
		'day_id'			=> 'día',
		'opening_time' 		=> 'apertura',
		'closing_time'		=> 'cierre',
		'service_type_id'	=> 'tipo de servicio'
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


}