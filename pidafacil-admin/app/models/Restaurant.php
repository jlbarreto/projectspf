<?php

use Cviebrock\EloquentSluggable\SluggableInterface;
use Cviebrock\EloquentSluggable\SluggableTrait;

class Restaurant extends \Eloquent implements SluggableInterface {

    use SluggableTrait;

    protected $table = 'res_restaurants';
    protected $primaryKey = 'restaurant_id';
    protected $sluggable = array(
        'build_from' => 'name',
        'save_to' => 'slug',
        'on_update' => false,
    );
    
    protected $fillable = [
        'name',
        'landing_page_id',
        'orders_allocator_id',
        'delivery_time',
        'guarantee_time',
        'shipping_cost',
        'minimum_order',
        'phone',
        'address',
        'map_coordinates',
        'search_reserved_position',
        'days_as_new'
    ];
    
    public static $reglas = array(
        'name' => 'required|min:5',
        'service_types'=>'required',
        'landing_page_id' => 'integer',
        'orders_allocator_id' => 'integer',
        'delivery_time' => 'required|integer',
        'guarantee_time' => 'required|integer',
        'shipping_cost' => 'numeric',
        'minimum_order' => 'numeric',
        'phone' => 'required|regex:/^[1-9]{1}[0-9]{3}-? ?[0-9]{4}$/i',
        'address' => 'required|min:10',
        'map_coordinates' => 'required',
        'search_reserved_position' => 'numeric',
        'days_as_new' => 'numeric',
        'commission_percentage' => 'required|between:0,99.99'
    );
    
    public static $presentacion = array(
        'name' => 'nombre',
        'landing_page_id' => 'landing page',
        'orders_allocator_id' => 'orders_allocator_id',
        'delivery_time' => 'tiempo de delivery',
        'guarantee_time' => 'tiempo de garatía',
        'shipping_cost' => 'costo de envío',
        'minimum_order' => 'mínimo de compra',
        'phone' => 'teléfono',
        'address' => 'dirección',
        'map_coordinates' => 'coordenadas de mapa',
        'search_reserved_position' => 'posición en búsqueda',
        'days_as_new' => 'día como nuevo'
    );

    public static function validacion($data) {
        //Se realiza la validación especificando las reglas arriba incluidas
        $validacion = Validator::make($data, static::$reglas);
        //Se proporcionan los nombres  los campos existentes
        $validacion->setAttributeNames(static::$presentacion);

        return $validacion;
    }

    public function landingpage() {
        return $this->hasOne('Webcontent', 'landing_page_id', 'landing_page_id');
    }

    public function sections() {
        return $this->hasMany('Section', 'restaurant_id', 'restaurant_id');
    }

    public function products() {
        return $this->hasManyThrough('Product', 'Section', 'restaurant_id', 'section_id');
    }

    public function services_types() {
        return $this->belongsToMany('ServiceType', 'res_restaurants_service_types', 'restaurant_id', 'service_type_id');
    }

    public function tags() {
        return $this->belongsToMany('Tag', 'res_tags', 'restaurant_id', 'tag_id');
    }

    public function ingredients() {
        return $this->hasMany('Ingredient', 'restaurant_id', 'restaurant_id');
    }

    public function schedules() {
        return $this->hasMany('Schedule', 'restaurant_id', 'restaurant_id');
    }

    public function conditionoptions() {
        return $this->belongsToMany('ConditionOption', 'res_restaurant_conditions_options', 'restaurant_id', 'condition_option_id')->withTimestamps();
    }
    
    public function getConditions() {
         $conditionOptions = $this->conditionoptions()->get();
        
        $conditions=array();
        
        foreach ($conditionOptions as $option){
            if(!array_key_exists($option->condition_id, $conditions)){
                $condition = $option->condition()->firstOrFail();
                $conditions[$condition->condition_id] = $condition;
            }
        }
        
        return $conditions;
    }

    public function paymentmethods() {
        return $this->belongsToMany('PaymentMethod', 'res_restaurants_payment_methods', 'restaurant_id', 'payment_method_id')->withTimestamps();
    }

    public function orders() {
        return $this->hasMany('Order', 'restaurant_id', 'restaurant_id');
    }

    public function users() {
        return $this->belongsToMany('User', 'res_restaurants_users');
    }


    public function contacts() {
        return $this->belongsToMany('Contact', 'res_restaurants_contacts', 'restaurant_id', 'contact_id');
    }
}
