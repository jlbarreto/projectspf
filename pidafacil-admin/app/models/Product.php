<?php
use Cviebrock\EloquentSluggable\SluggableInterface;
use Cviebrock\EloquentSluggable\SluggableTrait;

class Product extends \Eloquent implements SluggableInterface{
	use SluggableTrait;

	protected $table = 'res_products';
	protected $primaryKey = 'product_id';

	protected $sluggable = array(
        'build_from' => 'product',
        'save_to'    => 'slug',
        'on_update'  => true,
        'unique'     => false
    );		

    public static $reglas = array(
		'product' 		=> 'required|min:2', 
		'description'	=> 'required',
		'value' 		=> 'required|numeric',
		'section_id'	=> 'required|exists:res_sections,section_id',
                'imagen'     => 'image',
                'start_date' => 'date_format:"d/m/Y H:i"|regex:/[0-9]{2}\/[0-9]{2}\/[0-9]{4}.[0-9]{2}.[0-9]{2}/',
                'end_date' => 'date_format:"d/m/Y H:i"|regex:/[0-9]{2}\/[0-9]{2}\/[0-9]{4}.[0-9]{2}.[0-9]{2}/'
		);

    public static $presentacion = array(
		'product' 		=> 'producto', 
		'description'	=> 'descripción',
		'value' 		=> 'precio',
		'section_id'	=> 'sección',
		'activate'		=> 'activado',
                'imagen'     => 'imagen',
                'start_date' => 'Fecha de inicio',
                'end_date' => 'Fecha final'
		);

    public static function validacion($data){
		//Se realiza la validación especificando las reglas arriba incluidas
		$validacion = Validator::make($data, static::$reglas);
		//Se proporcionan los nombres  los campos existentes
		$validacion->setAttributeNames(static::$presentacion); 

		return $validacion;		
	}




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
        return $this->belongsToMany('Condition','res_products_conditions','product_id','condition_id')->withPivot('condition_order')->withTimestamps();
    }

    public function tags()
    {
        return $this->belongsToMany('Tag','res_product_tags','product_id','tag_id')->withTimestamps();
    }
	

}