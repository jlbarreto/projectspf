<?php

class Webcontent extends \Eloquent {

	protected $table = 'res_web_content';
	protected $primaryKey = 'landing_page_id';

	public static $reglas = array(
		'header' 		=> 'required|image|mimes:jpeg|max:1000', 
		'logo'			=> 'required|image|mimes:jpeg|max:1000',
		'banner'		=> 'required|image|mimes:jpeg|max:1000',
		'slogan'		=> 'required',
		'title_1'		=> 'required',
		'text_1'		=> 'required',
		'title_2'		=> 'required',
		'text_2'		=> 'required',
		'title_3'		=> 'required',
		'text_3'		=> 'required',
		'restaurant_id'	=> 'required|exists:res_restaurants,restaurant_id'
		);

    public static $presentacion = array(
		'title_1'	=> 'título 1',
		'text_1'	=> 'texto 1',
		'title_2'	=> 'título 2',
		'text_2'	=> 'texto 2',
		'title_3'	=> 'título 3',
		'text_3'	=> 'texto 3'
		);


    public static $mensaje = array(
    	'restaurant_id.required' => 'Ha surgido un error , contacte administrador',
    	'restaurant_id.integer'  => 'Ha surgido un error , contacte administrador',
    	'restaurant_id.exists'  =>  'El restaurante seleccionado no es válido'
    	);

    public static function validacion($data){
		//Se realiza la validación especificando las reglas arriba incluidas
		$validacion = Validator::make($data, static::$reglas, static::$mensaje);
		//Se proporcionan los nombres  los campos existentes
		$validacion->setAttributeNames(static::$presentacion); 

		return $validacion;		
	}


	public function restaurant()
    {
        return $this->belongsToMany('Restaurant' ,'landing_page_id','landing_page_id');
    }
	
}