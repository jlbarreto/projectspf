<?php

class IngredientController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET {$restslug}/ingredient
	 *
	 * @param string $restslug
	 * @return Response
	 */
	public function index($restslug)
	{
		$restaurant = Restaurant::where('slug',$restslug)->with('ingredients')->firstOrFail();
		return $restaurant;
	}

	/**
	 * Show the form for creating a new resource.
	 * GET {$restslug}/ingredient/create
	 *
	 * @return Response
	 */
	public function create($restslug)
	{
		$restaurant = Restaurant::where('slug',$restslug)->firstOrFail();
		$data['message'] = 'Formulario de creación de ingrediente';
		$data['error'] = false;
		return $data;
	}

	/**
	 * Store a newly created resource in storage.
	 * POST {$restslug}/ingredient/create
	 *
	 * @param string $restslug
	 * @return Response
	 */
	public function store($restslug)
	{
		$restaurant = Restaurant::where('slug',$restslug)->firstOrFail();
		$validation = Ingredient::validacion(Input::all());
		if($validation->fails()){
			return $validation->messages();
		}else{
			$count = Ingredient::where('ingredient',Input::get('ingredient'))->where('restaurant_id',$restaurant->restaurant_id)->count();
			if($count !== 0){
				$data['message'] = 'Error ingrediente ya existe';
				$data['error']  = true;

			}else{
				$ingredient = new Ingredient;
				$ingredient->ingredient = Input::get('ingredient');
				$ingredient = $restaurant->ingredients()->save($ingredient);
				$data['ingredient'] = $ingredient;
				$data['error']  = false;
			}
			return $data;
		}
	}

	/**
	 * Display the specified resource.
	 * GET {$restslug}/ingredient/{id}
	 *
	 * @param string $restslug
	 * @param  int  $id
	 * @return Response
	 */
	public function show($restslug,$id)
	{
		$restaurant = Restaurant::where('slug',$restslug)->firstOrFail();
		$ingredient = $restaurant->ingredients()->where('ingredient_id',$id)->firstOrFail();
		return $ingredient;
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET {$restslug}/ingredient/{id}/edit
	 *
	 * @param string $restslug
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($restslug,$id)
	{
		$restaurant = Restaurant::where('slug',$restslug)->firstOrFail();
		$ingredient = $restaurant->ingredients()->where('ingredient_id',$id)->firstOrFail();
		$data['restaurant'] = $restaurant;
		$data['ingredient'] = $ingredient;
		$data['error'] = false;
		$data['message'] = 'Formulario para edición de producto';
		return $data;
	}

	/**
	 * Update the specified resource in storage.
	 * POST {$restslug}/ingredient/{id}
	 *
	 * @param string $restslug
	 * @param  int  $id
	 * @return Response
	 */
	public function update($restslug,$id)
	{
		$restaurant = Restaurant::where('slug',$restslug)->firstOrFail();
		$ingredient = $restaurant->ingredients()->where('ingredient_id',$id)->firstOrFail();

		$validation = Ingredient::validacion(Input::all());
		if($validation->fails()){
			return $validation->messages();
		}else{
			$count = Ingredient::where('ingredient',Input::get('ingredient'))->where('restaurant_id',$restaurant->restaurant_id)->count();
			if($count !== 0){
				if($ingredient->ingredient === Input::get('ingredient')){
					$data['message'] = 'No se detectaron cambios';
					$data['error']  = false;
				}else{
					$data['message'] = 'Error ingrediente ya existe';
					$data['error']  = true;
				}

			}else{

				$ingredient->ingredient = Input::get('ingredient');
				$data['message'] = 'Producto editado exitosamente';
				$data['error']  = false;
			}
			return $data;
		}
		$data['restaurant'] = $restaurant;
		$data['ingredient'] = $ingredient;
		$data['error'] = false;

		return $data;
	}

	/**
	 * Remove the specified resource from storage.
	 * POST {$restslug}/ingredient/{id}/delete
	 *
	 * @param  string $restslug
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($restslug,$id)
	{
		
		
	}

}