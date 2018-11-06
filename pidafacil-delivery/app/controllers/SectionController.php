<?php

class SectionController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET {restslug}/section
	 *
	 * @param  string $restslug
	 * @return Response
	 */
	public function index($restslug)
	{
		$restaurant = Restaurant::where('slug',$restslug)->with('sections')->firstOrFail();
		return $restaurant;

	}

	/**
	 * Show the form for creating a new resource.
	 * GET {restslug}/section/create
	 *
	 * @param  string $restslug
	 * @return Response
	 */
	public function create($restslug)
	{
		$restaurant = Restaurant::where('slug',$restslug)->firstOrFail();

		return 'Formulario de creación de secciones para '.$restaurant->name.' ';
	}

	/**
	 * Store a newly created resource in storage.
	 * POST {restslug}/section
	 *
	 * @return Response
	 */
	public function store($restslug)
	{
		//********************************************
		//*      Falta verificar si el usuario       *
		//*      posee permisos para editar el       *
		//*		        RESTAURANTE                  *
		//********************************************
		$restaurant = Restaurant::where('slug',$restslug)->with('sections')->firstOrFail();
		$validacion = Section::validacion(Input::all());

		if($validacion->fails()){

			return $validacion->messages();

		}else{

			$section = new Section;
			$section->section = Input::get('section');
			$section->section_order_id = Input::get('section_order_id');
			$section = $restaurant->sections()->save($section);
			return $section;
		}		
	}

	/**
	 * Display the specified resource.
	 * GET {restslug}/section/{id}
	 *
	 * @param  int  $id
	 * @param  string $restslug
	 * @return Response
	 */
	public function show($restslug,$id)
	{
		$response['restaurant'] = Restaurant::where('slug',$restslug)->firstOrFail();
		$response['landingpage'] = $response['restaurant']->landingPage;
		$response['section'] = $response['restaurant']->sections()->where('section_id',$id)->firstOrFail();
		$response['products'] = $response['section']->products()->where('activate', 1)->get();
		return View::make('web.restaurant_section',$response);
		
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET {restslug}/section/{id}/edit
	 *
	 * @param  int  $id
	 * @param  string $restslug
	 * @return Response
	 */
	public function edit($restslug,$id)
	{
		$response['restaurant'] = Restaurant::where('slug',$restslug)->firstOrFail();
		$response['section'] = $response['restaurant']->sections()->where('section_id',$id)->firstOrFail();
		return $response;
	}

	/**
	 * Update the specified resource in storage.
	 * POST {restslug}/section/{id}/update
	 *
	 * @param  int  $id
	 * @param  string $restslug
	 * @return Response
	 */
	public function update($restslug,$id)
	{
		$restaurant = Restaurant::where('slug',$restslug)->firstOrFail();
		$section = $restaurant->sections()->where('section_id',$id)->firstOrFail();

		$validacion = Section::validacion(Input::all());
		if($validacion->fails()){

			return $validacion->messages();

		}else{

			$section->section = Input::get('section');
			$section->section_order_id = Input::get('section_order_id');
			$section->save();
			return $section;
		}
	}

	/**
	 * Remove the specified resource from storage.
	 * POST {restslug}/section/{id}/delete
	 *
	 * @param  int  $id
	 * @param  string $restslug
	 * @return Response
	 */
	public function destroy($restslug,$id)
	{
		$restaurant = Restaurant::where('slug',$restslug)->firstOrFail();
		$section = $restaurant->sections()->where('section_id',$id)->firstOrFail();

		$products = $section->products()->count();
		if($products > 0){
			return 'La sección tiene asignado/s '.$products.' producto/s, no puede eliminarse';
		}
		else
			$section->delete();

		return 'Cambios realizados con exito';
	}

}