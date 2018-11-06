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

	public function showWelcome()
	{
		$promociones = Product::where('activate',1)
		->where('promotion',1)->count();

		return View::make('web.home')
		->with('promociones',$promociones);
	}

	function zones() {
		return View::make('web.zones');
	}

	function zones_mobile() {
		return View::make('web.zones_mobile');
	}
}
