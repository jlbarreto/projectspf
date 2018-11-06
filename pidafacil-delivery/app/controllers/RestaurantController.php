<?php

class RestaurantController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /restaurant
	 *
	 * @return Response
	 */
	public function index()
	{
		$data['error'] = false;
		$data['data'] = Restaurant::all();
		return $data;	
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /restaurant/create
	 *
	 * @return Response
	 */
	public function create()
	{
		$data['error'] = false;
		$data['message'] = 'formulario de creacion web content';
		return $data;
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /restaurant
	 *
	 * @return Response
	 */
	public function store()
	{
		$validacion = Restaurant::validacion(Input::all());//Manejo de validación desde el modelo

		if($validacion->fails()){//comprobando el resultado de la validación

			return $validacion->messages();//Si existe algún error se retorna todos los errores

		} else {
			//----- Creación de un nuevo restaurante------
			$restaurant = new Restaurant;
			$restaurant->name = Input::get('name');
			$restaurant->orders_allocator_id = Input::get('orders_allocator_id');
			$restaurant->guarantee_time = Input::get('guarantee_time');
			$restaurant->delivery_time = Input::get('delivery_time');
			$restaurant->shipping_cost = Input::get('shipping_cost');
			$restaurant->minimum_order = Input::get('minimum_order');
			$restaurant->phone = Input::get('phone');
			$restaurant->address = Input::get('address');
			$restaurant->map_coordinates = Input::get('map_coordinates');
			$restaurant->search_reserved_position = Input::get('search_reserved_position');
			$restaurant->days_as_new = Input::get('days_as_new');
			$restaurant->save();
			if ( Input::get('parent_restaurant_id') == 0 ) { //Si es cero será el mismo
				$restaurant->parent_restaurant_id =  $restaurant->restaurant_id;
			}else{
				$restaurant->parent_restaurant_id =  Input::get('parent_restaurant_id');
			}
			$restaurant->save();

			
			return $restaurant;
			//-------Finalización de creación de restaurante------
		}
	}


	/**
	 * Display the specified resource.
	 * GET /{restslug}/
	 *
	 * @param  string  $restslug
	 * @return Response
	 */
	public function show($restslug)
	{

        if(strpos($restslug, "/"))
        {
           $explode = explode("/", $restslug);
            $producto= new ProductController();
            return $producto->show($explode[0],$explode[1]);

        }else
        {
            $restaurant = ParentRestaurant::getParent($restslug);
            $data['informacion'] =$restaurant;
            $data['landingpage'] = $restaurant->landingpage()->get();
            $data['sections'] = $restaurant->sections()->get();
            return View::make('web.restaurant',$data);
        }
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /{restslug}/edit
	 *
	 * @param  string  $restslug
	 * @return Response
	 */
	public function edit($restslug)
	{
		$data['error'] = false;
		$data['restaurant'] = Restaurant::where('slug',$restslug)->firstOrFail();
		//********************************************
		//*      Falta verificar si el usuario       *
		//*      posee permisos para editar el       *
		//*		        RESTAURANTE                  *
		//********************************************
		$data['message'] = 'Formulario para edicion de '. $data['restaurant']->name.' ';
		return $data;
	}

	/**
	 * Update the specified resource in storage.
	 * POST /{restslug}/update
	 *
	 * @param  string $restslug
	 * @return Response
	 */
	public function update($restslug)
	{
		$validacion = Restaurant::validacion(Input::all());

		if($validacion->fails()){

			return $validacion->messages();

		} else {
			//-----UPDATE restaurante------

			$restaurant = Restaurant::where('slug',$restslug)->firstOrFail();
			//********************************************
			//*      Falta verificar si el usuario       *
			//*      posee permisos para editar el       *
			//*		        RESTAURANTE                  *
			//********************************************
			$restaurant->name = Input::get('name');
			$restaurant->orders_allocator_id = Input::get('orders_allocator_id');
			$restaurant->guarantee_time = Input::get('guarantee_time');
			//$restaurant->landing_page_id = Input::get('landing_page_id');
			$restaurant->delivery_time = Input::get('delivery_time');
			$restaurant->shipping_cost = Input::get('shipping_cost');
			$restaurant->minimum_order = Input::get('minimum_order');
			$restaurant->phone = Input::get('phone');
			$restaurant->address = Input::get('address');
			$restaurant->map_coordinates = Input::get('map_coordinates');
			$restaurant->search_reserved_position = Input::get('search_reserved_position');
			$restaurant->days_as_new = Input::get('days_as_new');
			$restaurant->save();
			return $restaurant;
			//-------UPDATE restaurante------
		}
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /restaurant/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	/**
	 * Show the shedule of  specified restaurant.
	 * GET /{restslug}/shedules
	 *
	 * @param  string $restslug
	 * @return Response
	 */
	public function showschedule($restslug)
	{
		$restaurant = Restaurant::where('slug',$restslug)->firstOrFail();
		$parent_restaurant = Restaurant::where('restaurant_id',$restaurant->parent_restaurant_id)->firstOrFail();

		$data['message'] = 'Formulario para la edición de horarios de restaurante '.$restaurant->name;
		$data['error'] = false;
		$data['schedule'] =  $parent_restaurant->schedules()->get();
		return $data;
	}

	/**
	 * Cretate shedule of  specified restaurant.
	 * POST /{restslug}/shedules
	 *
	 * @param  string $restslug
	 * @return Response
	 */
	public function createschedule($restslug)
	{
		$restaurant = Restaurant::where('slug',$restslug)->firstOrFail();
		$validacion = Schedule::validacion(Input::all());

		if($validacion->fails()){

			return $validacion->messages();

		} else {

			$existencia = $restaurant->schedules()->where('day_id',Input::get('day_id'))->where('service_type_id',Input::get('service_type_id'))->count();
			
			if( $existencia == 1) {
				$data['message'] = 'Tipo de horario ya existente';
				$data['error'] = true;

				return $data; 

			} else {

				$schedule = new Schedule;
				$schedule->restaurant_id = $restaurant->restaurant_id;
				$schedule->day_id = Input::get('day_id');
				$schedule->opening_time = Input::get('opening_time');
				$schedule->closing_time = Input::get('closing_time');
				$schedule->service_type_id = Input::get('service_type_id');
				
				$schedule->save();
				return $schedule;

			}
		}
	}

	/**
	 * Cretate shedule of  specified restaurant.
	 * POST /{restslug}/shedules/update/{id}
	 *
	 * @param  string $restslug
	 * @return Response
	 */
	public function storeschedule($restslug,$id)
	{
		$restaurant = Restaurant::where('slug',$restslug)->firstOrFail();
		$schedule = Schedule::find($id);
		$schedule->opening_time = Input::get('opening_time');
		$schedule->closing_time = Input::get('closing_time');
		$schedule->save();
		return $schedule;


	}

	/**
	 * Cretate shedule of  specified restaurant.
	 * GET /{restslug}/shedules/update/{id}
	 *
	 * @param  string $restslug
	 * @return Response
	 */
	public function updateschedule($restslug,$id)
	{
		$restaurant = Restaurant::where('slug',$restslug)->firstOrFail();
		$schedule = $restaurant->schedules()->where('schedule_id',$id)->get();
		$data['message'] = 'Formulario para update de horario';
		$data['error'] = false;
		$data['schedule'] = $schedule;
		return $data;

	}

	/**
	 * Create shedule of  specified restaurant.
	 * POST /{restslug}/shedules/delete/{id}
	 *
	 * @param  string $restslug
	 * @return Response
	 */
	public function deleteschedule($restslug,$id)
	{
		$restaurant = Restaurant::where('slug',$restslug)->firstOrFail();
		Schedule::destroy($id);
		$data['message'] = 'Operación completada con éxito';
		$data['error'] = false;
		return $data;
	}

	/**
	 * Add paymenth method.
	 * POST /{restslug}/payment-method
	 *
	 * @param  string $restslug
	 * o@return Response
	 */
	public function paymentmethod($restslug)
	{

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
	public function about($restslug)
	{

		$data['restaurant_info'] = ParentRestaurant::getParent($restslug);
		$data['landingpage']=$data['restaurant_info']->landingpage()->get();
		$data['schedules']=$data['restaurant_info']->schedules()->get();
		return View::make('web.restaurant_info',$data);

	
	}

	/**
	 * Display a listing of the resource by promos.
	 * GET /{restslug}/promociones
	 *
	 * @return Response
	 */
	public function promos($restslug)
	{
		// Show  promos by restaurant
		$response['restaurant'] = Restaurant::where('slug',$restslug)->firstOrFail();
		$response['landingpage'] = $response['restaurant']->landingPage;
		$response['products'] = Restaurant::find($response['restaurant']->restaurant_id)
			->products()->where('activate', true)
			->where('promotion', true)->get();
			
		return View::make('web.promo_products',$response);
	}
}