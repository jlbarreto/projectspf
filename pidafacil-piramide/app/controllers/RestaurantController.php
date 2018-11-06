<?php

class RestaurantController extends \BaseController {

    /**
     * Display a listing of the resource.
     * GET /restaurant
     *
     * @return Response
     */
    public function index() {
        $data['error'] = false;
        $data['data'] = Restaurant::all();
        return $data;
    }


    /**
     * Display the specified resource.
     * GET /{restslug}/
     *
     * @param  string  $restslug
     * @return Response
     */
    public function show($restslug) {
        $restaurant = ParentRestaurant::getParent($restslug);
        $secion = Section::where('restaurant_id',$restaurant->restaurant_id)->get();

        $promociones = Product::where('activate',1)
            ->where('promotion',1)
            ->where('section_id',$secion[0]->restaurant_id)
            ->count();

        $data['informacion'] = $restaurant;
        $data['landingpage'] = $restaurant->landingpage()->get();
        $data['sections'] = $restaurant->sections()->where('activate', '=',  true)->orderBy('section_order_id', 'asc')->get();
        $data['promociones'] = $promociones;
        return View::make('web.restaurant', $data);
    }


    public static function arrDias() {
        return array(1 => 'Domingo', 2 => 'Lunes', 3 => 'Martes', 4 => 'Miércoles', 5 => 'Jueves', 6 => 'Viernes', 7 => 'Sábado');
    }
    /**
     * Show the shedule of  specified restaurant.
     * GET /{restslug}/shedules
     *
     * @param  string $restslug
     * @return Response
     */
    public function showschedule($restslug) {
        $restaurant = Restaurant::where('slug', $restslug)->firstOrFail();
        $parent_restaurant = Restaurant::where('restaurant_id', $restaurant->parent_restaurant_id)->firstOrFail();

        $data['message'] = 'Formulario para la edición de horarios de restaurante ' . $restaurant->name;
        $data['error'] = false;
        $data['schedule'] = $parent_restaurant->schedules()->get();
        return $data;
    }

    /**
     * Add paymenth method.
     * POST /{restslug}/payment-method
     *
     * @param  string $restslug
     * o@return Response
     */
    public function paymentmethod($restslug) {

        $restaurant = ParentRestaurant::getParent($restslug);
        return $restaurant->paymentmethods()->get();
    }

    /**
     * Add paymenth method.
     * GET /{restslug}/about
     *
     * @param  string $restslug
     * o@return Response
     */
    public function about($restslug) {

        $data['restaurant_info'] = ParentRestaurant::getParent($restslug);
        $data['landingpage'] = $data['restaurant_info']->landingpage()->get();
        
        $data['sections'] = $data['restaurant_info']->sections()->where('activate', true)->orderBy('section_order_id', 'asc')->get();
        
        $schedules = $data['restaurant_info']->schedules()->get();
        $data['schedules'] = array();
        
        foreach ($schedules as $schedule) {
            $data['schedules'][$schedule->day_id][$schedule->service_type_id]=array('opening'=> $schedule->opening_time, 'closing'=> $schedule->closing_time);
        }
        
        $data['service_types'] = $data['restaurant_info']->services_types()->get();
        $data['arr_dias'] = $this->arrDias();
        
        $data['sucursales'] = Restaurant::where('parent_restaurant_id', '=', $data['restaurant_info']->restaurant_id)->get();
        
        $count = count($data['sucursales']);
        
        if($count>1){
            for ($i=0; $i<$count; $i++) {
                if($data['sucursales'][$i]->restaurant_id==$data['restaurant_info']->restaurant_id){
                    unset($data['sucursales'][$i]);
                }
            }
        }
        
        return View::make('web.restaurant_info', $data);
    }

    /**
     * Display a listing of the resource by promos.
     * GET /{restslug}/promociones
     *
     * @return Response
     */

    public function promos($restslug) {
        // Show  promos by restaurant
        $response['restaurant'] = Restaurant::where('slug', $restslug)->firstOrFail();
        $response['landingpage'] = $response['restaurant']->landingPage;
        $response['products'] = Restaurant::find($response['restaurant']->restaurant_id)
                        ->products()->where('res_products.activate', true)
                        ->where('promotion', true)->get();

        return View::make('web.promo_products', $response);
    }

}
