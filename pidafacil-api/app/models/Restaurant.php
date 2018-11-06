<?php

class Restaurant extends \Eloquent {

	protected $table = 'res_restaurants';
	protected $primaryKey = 'restaurant_id';
	
	public function categories()
    {
		return $this->belongsToMany('Tag', 'res_tags', 'restaurant_id', 'tag_id');
	}

	public function products()
    {
        return $this->hasManyThrough('Product', 'Section', 'restaurant_id', 'section_id');
    }

    public function landingPage()
    {
        return $this->hasOne('WebContent','landing_page_id','landing_page_id');
    }
    
    public function sections()
    {
        return $this->hasMany('Section','restaurant_id','restaurant_id');
    }

    public function serviceTypes()
    {
        return $this->belongsToMany('ServiceType','res_restaurants_service_types')->orderBy("service_type")->withTimestamps();
    }

    public function paymentMethods()
    {
        return $this->belongsToMany('PaymentMethod','res_restaurants_payment_methods')->withTimestamps();
    }

    public function schedules()
    {
        return $this->hasMany('Schedule', 'restaurant_id', 'restaurant_id');
    }
    
    /**
     * Verifica que un restaurante esté abiero a dicha hora, sino se manda 
     * la hora se toma la hora del servidor, siempre evalua el día de ahora
     */
    public static function isOpen($restaurant_id, $service_type_id, $time=null){
        $is_open = false;
        
        if($time==null){
            $timezone = date_default_timezone_get();
            $time = date('G:i:s', time());
        }
        
        $count = Schedule::where('restaurant_id', $restaurant_id)
                                    ->where('service_type_id', $service_type_id)
                                    ->where('day_id', date('w')+1)->count();
        
        if($count>0){
            $schedules = Schedule::where('restaurant_id', $restaurant_id)
                        ->where('service_type_id', $service_type_id)
                        ->where('day_id', date('w')+1)->first();
            
            $open = strtotime($schedules->opening_time);
            $close = strtotime($schedules->closing_time);
            $t = strtotime($time);
            
            if($open<$close){
                //Si el horario es normal
                if($t>$open and $t<$close){
                    $is_open=true;
                }
            }else{
                //Si el horario pasa de las 24 horas
                if($t>$open or $t<$close){
                    $is_open=true;
                }
            }
        }
        
        return $is_open;
    }
}