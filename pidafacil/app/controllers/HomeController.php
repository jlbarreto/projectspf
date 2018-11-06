<?php

class HomeController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function showWelcome(){

		$date = new DateTime();
        $fecha = $date->format('Y-m-d H:i:s');

		$promociones = Product::where('activate',1)
			->where('promotion',1)
			->where('res_products.start_date', '<=', $fecha)->where('res_products.end_date', '>=', $fecha)
			->count();

		return View::make('web.home')
		->with('promociones',$promociones);
	}

	function zones(){
		
		$date = new DateTime();
        $fecha = $date->format('Y-m-d H:i:s');

		$promociones = Product::where('activate',1)
		->where('promotion',1)
		->where('res_products.start_date', '<=', $fecha)->where('res_products.end_date', '>=', $fecha)
		->count();

		$zonas = DB::table('com_zones')->orderBy('zone_id', 'asc')->get();

		return View::make('web.zones')
			->with('promociones', $promociones)
			->with('zonas', $zonas);
	}

	function zones_mobile(){
		$zonas = DB::table('com_zones')->get();

		return View::make('web.zones_mobile')->with('zonas', $zonas);
	}
}
