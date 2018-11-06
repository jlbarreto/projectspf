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
	public function edit($restslug)
	{
		$data['restaurant'] = Restaurant::where('slug',$restslug)->firstOrFail();
		$data['sections_selected'] = $data['restaurant']->sections()->orderBy('section_order_id', 'asc')->get(); 
                
                if(Session::has('wizard')){
                    Session::forget('wizard');
                    Session::forget('step');
                    Session::forget('restaurant_slug');
                }
                
                return View::make('admin.restaurant_section')->with($data);
	}
        
        public function add($restslug)
	{
		$restaurant = Restaurant::where('slug',$restslug)->firstOrFail();
		
                if(Input::get('id')!=0){
                    $section = Section::find(Input::get('id'));
                }else{
                    $section = new Section();
                    
                    $section_last = $restaurant->sections()->max('section_order_id');
                    $section->section_order_id=$section_last+1;
                }
                    
                $section->restaurant_id=$restaurant->restaurant_id;
                $section->section = Input::get('name');
                
                $section->save();
                Session::flash('flash_message', 'Sección almacenada con éxito');
                
                return Redirect::back();
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
        
        public function positions() {
            $arPositions = Input::get('position');
            foreach ($arPositions as $key => $value) {
                $section = Section::find($key);
                $section->section_order_id=$value;
                $section->save();
            }
            
            Session::flash('flash_message', 'Las posiciones fueron actualizadas con éxito');
                
            return Redirect::back();
        }

	/**
	 * Remove the specified resource from storage.
	 * POST {restslug}/section/{id}/delete
	 *
	 * @param  int  $id
	 * @param  string $restslug
	 * @return Response
	 */
	public function destroy($id)
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
        
        public function activate($id)
	{
		$section = Section::where('section_id',$id)->firstOrFail();

		if($section->activate == 0){
                    $section->activate=1;
                    Session::flash('flash_message', 'Sección activada');
		}
		else{
                    $section->activate=0;
                    Session::flash('flash_message', 'Sección desactivada');
                }
                
                $section->save();

		return Redirect::back();
	}

}