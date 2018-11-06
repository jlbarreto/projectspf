<?php

class WebcontentController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /{slug}/webcontent
	 *
	 * @return Response
	 */
	public function index(){
		$data['error'] = false;
		$data['message'] = 'Mostrar recursos que se tiene o en su defecto editarlos';
		return $data;
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /{slug}/webcontent/create
	 *
	 * @return Response
	 */
	public function create(){
		//-----------
		$data['error'] = false;
		$data['message'] = 'formulario de creación web content';
		return $data;
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /{slug}/webcontent
	 *
	 * @return Response
	 */
	public function store(){
		$validacion = Webcontent::validacion(Input::all());
		if($validacion->fails()){

			return $validacion->messages();

		} else {
			$restaurant = Restaurant::find(Input::get('restaurant_id'));
			$webcontent = new Webcontent;

			$header = Input::file('header');
			$destinationPath = '/restaurants/'.$restaurant->slug.'/';
			$uploadSuccess = $header->move(public_path().$destinationPath, 'header.jpeg');

			if($uploadSuccess)
				$webcontent->header = $destinationPath.'header.jpeg';
			else
				return 'error al subir header';
			
			
			$logo =  Input::file('logo');
			$uploadSuccess = $logo->move(public_path().$destinationPath, 'logo.jpeg');

			if($uploadSuccess)
				$webcontent->logo = $destinationPath.'logo.jpeg';
			else
				return 'error al subir logo';
			

			$banner =  Input::file('banner');
			$uploadSuccess = $banner->move(public_path().$destinationPath, 'banner.jpeg');

			if($uploadSuccess)
				$webcontent->banner = $destinationPath.'banner.jpeg';
			else
				return 'error al subir banner';

			$webcontent->slogan = Input::get('slogan');
			$webcontent->title_1= Input::get('title_1');
			$webcontent->title_2= Input::get('title_2');
			$webcontent->title_3= Input::get('title_3');
			$webcontent->text_1= Input::get('text_1');
			$webcontent->text_2= Input::get('text_2');
			$webcontent->text_3= Input::get('text_3');
			$webcontent->save();

			$restaurant->landing_page_id = $webcontent->landing_page_id;
			$restaurant->save();
			
		}
	}

	/**
	 * Display the specified resource.
	 * GET /webcontent/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /{restslug}/webcontent/edit
	 *
	 * @param  string  $restslug
	 * @return Response
	 */
	public function edit($restslug){
		$restaurant = Restaurant::where('slug',$restslug)->firstOrFail();
		$webcontent = Webcontent::find($restaurant->landing_page_id);

		return 'Formulario para editar langind page de \''.$restaurant->name.'\' con lading page id '.$webcontent->landing_page_id;
	}

	/**
	 	* Update the specified resource in storage.
	 	* POST /{restslug}/webcontent/update
	 	*
	 	* @param  string  $restslug
	 	* @return Response
 	*/
	public function update($restslug){
		//********************************************
		//*      Falta verificar si el usuario       *
		//*      posee permisos para editar el       *
		//*		         WebContent                  *
		//********************************************
		$restaurant = Restaurant::where('slug',$restslug)->firstOrFail();
		$webcontent = Webcontent::find($restaurant->landing_page_id);
		
		$header = Input::file('header');
		$logo 	= Input::file('logo');
		$banner = Input::file('banner');

		if(!empty($header)){ //verificar si la variable viene vacía

			File::delete(public_path().$webcontent->header);
			$destinationPath = '/restaurants/'.$restaurant->slug.'/';
			$uploadSuccess = $header->move(public_path().$destinationPath, 'header.jpeg');

			if($uploadSuccess)
				$webcontent->header = $destinationPath.'header.jpeg';
			else
				return 'error al subir header';
		}

		if(!empty($logo)){

			File::delete(public_path().$webcontent->logo);
			$uploadSuccess = $logo->move(public_path().$destinationPath, 'logo.jpeg');

			if($uploadSuccess)
				$webcontent->logo = $destinationPath.'logo.jpeg';
			else
				return 'error al subir logo';
		}

		if(!empty($banner)){

			File::delete(public_path().$webcontent->banner);
			$uploadSuccess = $banner->move(public_path().$destinationPath, 'banner.jpeg');

			if($uploadSuccess)
				$webcontent->banner = $destinationPath.'banner.jpeg';
			else
				return 'error al subir banner';

		}

		$webcontent->slogan = Input::get('slogan');
		$webcontent->title_1= Input::get('title_1');
		$webcontent->title_2= Input::get('title_2');
		$webcontent->title_3= Input::get('title_3');
		$webcontent->text_1= Input::get('text_1');
		$webcontent->text_2= Input::get('text_2');
		$webcontent->text_3= Input::get('text_3');
		$webcontent->save();

		$restaurant->landing_page_id = $webcontent->landing_page_id;
		$restaurant->save();

	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /webcontent/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id){
		//
	}

}