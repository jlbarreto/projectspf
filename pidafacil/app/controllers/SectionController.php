<?php

class SectionController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET {restslug}/section
	 *
	 * @param  string $restslug
	 * @return Response
	 */
	public function index($restslug){
		$restaurant = Restaurant::where('slug',$restslug)->with('sections')->firstOrFail();
		return $restaurant;
	}

	/**
	 * Display the specified resource.
	 * GET {restslug}/section/{id}
	 *
	 * @param  int  $id
	 * @param  string $restslug
	 * @return Response
	 */
	public function show($restslug,$id){	
		$date = new DateTime();
        $fecha = $date->format('Y-m-d H:i:s');

        $promociones = Product::where('activate',1)
					        ->where('promotion',1)
					        ->where('res_products.start_date', '<=', $fecha)->where('res_products.end_date', '>=', $fecha)
					        ->count();
		
		$response['restaurant'] = Restaurant::where('slug',$restslug)->firstOrFail();
		$response['landingpage'] = $response['restaurant']->landingPage;
		$response['section'] = $response['restaurant']->sections()->where('section_id',$id)->firstOrFail();
		$response['products'] = $response['section']->products()->where('activate', 1)->get();
		$response['sections'] = $response['restaurant']->sections()->where('activate', true)->orderBy('section_order_id', 'asc')->get();
		return View::make('web.restaurant_section',$response)->with('promociones', $promociones);	
	}

}