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
        $test = substr_count($restslug, '/');
        #echo $test; #die();
        $rest = strstr($restslug, '/', true);
        $date = new DateTime();
        $fecha = $date->format('Y-m-d H:i:s');

        if($test==0){
            $flag=1;
            $restaurant = ParentRestaurant::getParent($restslug);
            $secion = Section::where('restaurant_id',$restaurant->restaurant_id)->get();
            
            $promociones = Restaurant::with('landingPage')
                ->where('restaurant_id',$restaurant->restaurant_id)
                ->whereHas('products', function($q) {
                    $date = new DateTime();
                    $fecha = $date->format('Y-m-d H:i:s');
                
                    $q->where('promotion', true)
                        ->where('res_products.start_date', '<=', $fecha)
                        ->where('res_products.end_date', '>=', $fecha)
                        ->orderBy('res_products.created_at', 'desc');
                })
                ->count();

            $free = DB::SELECT('
                SELECT * FROM pf.free_shipping_restaurants
            ');

            $data['free'] = $free;
            $data['restaurant_id'] = $restaurant->restaurant_id;
            $data['informacion'] = $restaurant;
            $data['landingpage'] = $restaurant->landingpage()->get();
            $data['sections'] = $restaurant->sections()->where('activate', '=',  true)->orderBy('section_order_id', 'asc')->get();
            $data['promociones'] = $promociones;
            Log::info($data['restaurant_id']);
            return View::make('web.restaurant', $data);

        }else{
            $prueba = explode('/', $restslug);
            $prod = $prueba[1];

            $response['restaurant'] = ParentRestaurant::getParent($rest);
            $response['landingpage'] = $response['restaurant']->landingPage;
            $response['sections'] = $response['restaurant']->sections()->where('activate', true)->orderBy('section_order_id', 'asc')->get();
            $response['product'] = $response['restaurant']->products()->where('slug', $prod)->firstOrFail();
            $response['condition'] = $response['product']->conditions()
                    ->with(['opciones'=>function($query){
                        $query->where('active', 1);
                    }])->orderBy('condition_order', 'ASC')->get();
            $response['ingredient'] = $response['product']->ingredients()
                                                          ->where('active', 1)
                                                          ->orderBy('position', 'asc')
                                                          ->get();
            $promociones = Product::where('activate',1)
                                ->where('promotion',1)
                                ->where('res_products.start_date', '<=', $fecha)->where('res_products.end_date', '>=', $fecha)
                                ->count();
                                                          
            return View::make('web.products', $response)->with('promociones', $promociones);
        }
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
        
        $data['sucursales'] = Restaurant::where('parent_restaurant_id', '=', $data['restaurant_info']->restaurant_id)->where('activate', 1)->get();
        
        $count = count($data['sucursales']);
        
        if($count>1){
            for ($i=0; $i<$count; $i++) {
                if($data['sucursales'][$i]->restaurant_id==$data['restaurant_info']->restaurant_id){
                    unset($data['sucursales'][$i]);
                }
            }
        }

        $date = new DateTime();
        $fecha = $date->format('Y-m-d H:i:s');

        $promociones = Product::where('activate',1)
        ->where('promotion',1)
        ->where('res_products.start_date', '<=', $fecha)->where('res_products.end_date', '>=', $fecha)
        ->count();
        
        return View::make('web.restaurant_info', $data)->with('promociones', $promociones);
    }

    /**
     * Display a listing of the resource by promos.
     * GET /{restslug}/promociones
     *
     * @return Response
     */

    public function promos($restslug) {
        // Show  promos by restaurant
        $date = new DateTime();
        $fecha = $date->format('Y-m-d H:i:s');
        
        $response['restaurant'] = Restaurant::where('slug', $restslug)->firstOrFail();
        $response['landingpage'] = $response['restaurant']->landingPage;
        $response['products'] = Restaurant::find($response['restaurant']->restaurant_id)
                        ->products()->where('res_products.activate', true)
                        #->whereRaw(" '".$fecha."' between res_products.start_date and res_products.end_date")
                        ->where('res_products.start_date', '<=', $fecha)->where('res_products.end_date', '>=', $fecha)
                        ->where('promotion', true)
                        ->get();
                        #echo $response['products']; die();

        $date = new DateTime();
        $fecha = $date->format('Y-m-d H:i:s');

        $promociones = Product::where('activate',1)
        ->where('promotion',1)
        ->where('res_products.start_date', '<=', $fecha)->where('res_products.end_date', '>=', $fecha)
        ->count();

        return View::make('web.promo_products', $response)->with('promociones', $promociones);
    }

}
