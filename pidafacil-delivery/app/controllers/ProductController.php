<?php

class ProductController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /{restslug}
	 *
	 * @return Response
	 */
	public function index($restslug)
	{
		//Ya se maneja en restaurante 
		$restaurant = ParentRestaurant::getParent($restslug);
		return $restaurant;
	}

	/**
	 * Show the form for creating a new resource.
	 * GET {restslug}/product/create
	 *
	 * @param  string  $restslug
	 * @return Response
	 */
	public function create($restslug)
	{
		$restaurant = ParentRestaurant::getParent($restslug);
		
		return 'crear producto para restaurante '.$restaurant->name.' '; 
	}

	/**
	 * Store a newly created resource in storage.
	 * POST {restslug}/product/create
	 *
	 * @return Response
	 */
	public function store($restslug)
	{
		$restaurant = ParentRestaurant::getParent($restslug);
		//--------Validación -----------------------
		$validation = Product::validacion(Input::all());
		//-------------------------------------------
		if($validation->fails()){
			return $validation->messages();

		}else{

			$section = $restaurant->sections()->where('section_id',Input::get('section_id'))->count();
			if($section == 1){
				$prodslug = Str::slug(Input::get('product'), '-');
				$unicidad = $restaurant->products()->where('slug',$prodslug)->count();
				if($unicidad > 0 )
					return 'Existe un producto con el mismo nombre dentro de su restaurante';
				else{

					//Creación de producto
					$product = new Product;
					$product->product = Input::get('product');
					$product->description = Input::get('description');
					$product->value	= Input::get('value');
					$product->section_id = Input::get('section_id');
					$product->activate = Input::get('activate');
					$product->promotion = Input::get('promotion');
					$product->start_date = Input::get('start_date');
					$product->end_date= Input::get('end_date');
					//----manejo de ingredientes-----
					$product->save();

					$validator = Validator::make(
					    array('image_web' => Input::file('image_web')),
					    array('image_web' => 'image|mimes:jpeg|max:1000')
					);

					if(!$validator->fails()){
						$image_web = Input::file('image_web');
						$destinationPath = '/restaurants/'.$restaurant->slug.'/'.$product->slug;
						$uploadSuccess = $image_web->move(public_path().$destinationPath, 'image_web.jpeg');

						if($uploadSuccess)
							$product->image_web = $destinationPath.'/image_web.jpeg';
						else
							return 'error al subir image_app';
						
					}
					$product->save();

					$validator2 = Validator::make(
					    array('image_app' => Input::file('image_app')),
					    array('image_app' => 'image|mimes:jpeg|max:1000')
					);

					if(!$validator2->fails()){
						$image_web = Input::file('image_app');
						$destinationPath = '/restaurants/'.$restaurant->slug.'/'.$product->slug;
						$uploadSuccess = $image_web->move(public_path().$destinationPath, 'image_app.jpeg');

						if($uploadSuccess)
							$product->image_app = $destinationPath.'/image_app.jpeg';
						else
							return 'error al subir image_app';
						
					}
					$product->save();

					return $product;
				}
				
			}else
				return 'El campo sección seleccionado es inválido.'; //sección equivocada

				
		}
	}

	/**
	 * Display the specified resource.
	 * GET /{restslug}/{prodslug}
	 *
	 * @param  string  $restslug
	 * @param  string  $prodslug
	 * @return Response
	 */
	public function show($restslug,$prodslug)
	{
		$response['restaurant'] = ParentRestaurant::getParent($restslug);
		$response['landingpage'] = $response['restaurant']->landingPage;
		$response['product'] = $response['restaurant']->products()->where('slug',$prodslug)->firstOrFail();
		$response['condition'] = $response['product']->conditions()->with('opciones')->orderBy('condition_order', 'ASC')->get();
		$response['ingredient'] = $response['product']->ingredients()->get();
		return View::make('web.products',$response);		
		return $response;
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /{restslug}/{prodslug}/edit
	 *
	 * @param  string  $restslug
	 * @param  string  $prodslug
	 * @return Response
	 */
	public function edit($restslug,$prodslug)
	{
		$response['restaurant'] = ParentRestaurant::getParent($restslug);
		$response['product'] = $response['restaurant']->products()->where('slug',$prodslug)->firstOrFail();
		$response['message'] = 'Formulario para editar producto';
		return $response;
	}

	/**
	 * Update the specified resource in storage.
	 * POST /{restslug}/{prodslug}/update
	 *
	 * @param  string  $restslug
	 * @param  string  $prodslug
	 * @return Response
	 */
	public function update($restslug,$prodslug)
	{
		$restaurant = ParentRestaurant::getParent($restslug);
		$product = $restaurant->products()->where('slug',$prodslug)->firstOrFail();
		$section = $product->section();
	
		$validacion = Product::validacion(Input::all());
		if($validacion->fails()){

			return $validacion->messages();

		}else{

			if (strcasecmp($product->product, Input::get('product')) == 0){

				$product->description = Input::get('description');
				$product->value	= Input::get('value');
				$product->section_id = Input::get('section_id');
				$product->activate = Input::get('activate');
				$product->save();

			}else{

				$newprodslug = Str::slug(Input::get('product'), '-');
				$unicidad = $restaurant->products()->where('slug',$newprodslug)->count();
				if($unicidad > 0 )
					return 'Existe un producto con el mismo nombre dentro de su restaurante';
				else{
					//Creación de producto
					$product->product = Input::get('product');
					$product->description = Input::get('description');
					$product->value	= Input::get('value');
					$product->section_id = Input::get('section_id');
					$product->activate = Input::get('activate');
					$product->save();
				}
			}

			$validator = Validator::make(
			    array('image_web' => Input::file('image_web')),
			    array('image_web' => 'image|mimes:jpeg|max:1000')
			);

			if(!$validator->fails()){
				$image_web = Input::file('image_web');
				$destinationPath = '/restaurants/'.$restaurant->slug.'/'.$product->slug;
				$uploadSuccess = $image_web->move(public_path().$destinationPath, 'image_web.jpeg');

				if($uploadSuccess)
					$product->image_web = $destinationPath.'/image_web.jpeg';
				else
					return 'error al subir image_app';
				
			}
			$product->save();

			$validator2 = Validator::make(
			    array('image_app' => Input::file('image_app')),
			    array('image_app' => 'image|mimes:jpeg|max:1000')
			);

			if(!$validator2->fails()){
				$image_web = Input::file('image_app');
				$destinationPath = '/restaurants/'.$restaurant->slug.'/'.$product->slug;
				$uploadSuccess = $image_web->move(public_path().$destinationPath, 'image_app.jpeg');

				if($uploadSuccess)
					$product->image_app = $destinationPath.'/image_app.jpeg';
				else
					return 'error al subir image_app';
			}
			$product->save();

			$response['product'] = $product;
			//$response['restaurant'] = $restaurant;
			return $response;

		}

		
	}

	/**
	 * Remove the specified resource from storage.
	 * POST /{restslug}/{prodslug}/delete
	 *
	 * @param  string  $restslug
	 * @param  string  $prodslug
	 * @return Response
	 */
	public function destroy($restslug,$prodslug)
	{
		$restaurant = ParentRestaurant::getParent($restslug);
		$product = $restaurant->products()->where('slug',$prodslug)->firstOrFail();
		$product->delete();
		$response['restaurant'] = $restaurant;
		$response['message'] = 'éxito';

		return $response;

	}

	/**
	 * Show product ingredients.
	 * GET /{restslug}/{prodslug}/ingredients
	 *
	 * @param  string  $restslug
	 * @param  string  $prodslug
	 * @return Response
	 */
	public function showingredient($restslug,$prodslug)
	{
		$restaurant = ParentRestaurant::getParent($restslug);
		$product = $restaurant->products()->where('slug',$prodslug)->firstOrFail();
		return $product->ingredients;

	}

	/**
	 * Add ingredients to a product.
	 * POST /{restslug}/{prodslug}/ingredients
	 *
	 * @param  string  $restslug
	 * @param  string  $prodslug
	 * @return Response
	 */
	public function addingredient($restslug,$prodslug)
	{
		$restaurant = ParentRestaurant::getParent($restslug);
		$product = $restaurant->products()->where('slug',$prodslug)->firstOrFail();

		$exist = $restaurant->ingredients()->where('ingredient_id',Input::get('ingredient'))->count();
		if($exist == 1){  //verificando la existencia del producto

			$ingredient_id = Input::get('ingredient');
			$removable = Input::get('removable');
			$ingredients = $product->ingredients;

			if($ingredients->contains($ingredient_id)){// Verificando si ya existe la relación previamente

				$data['message'] = 'El ingrediente ya ha sido añadido';
				$data['error'] = true;
			
			}else{

				//Si no existe se crea
				$product->ingredients()->attach($ingredient_id, array('removable' => $removable));
				$data['message'] = 'Operación realizada con éxito';
				$data['error'] = false;

			}

		}else{

			$data['message'] = 'Error al procesar ingrediente';
			$data['error'] = true;
			
		}

		return $data;

	}

	/**
	 * Add ingredients to a product.
	 * POST /{restslug}/{prodslug}/ingredients/{id}/delete
	 *
	 * @param  string  $restslug
	 * @param  string  $prodslug
	 * @param  integer $id
	 * @return Response
	 */
	public function deleteingredient($restslug,$prodslug,$ingredient_id)
	{
		$restaurant = ParentRestaurant::getParent($restslug);
		$product = $restaurant->products()->where('slug',$prodslug)->firstOrFail();
		$exist = $restaurant->ingredients()->where('ingredient_id',$ingredient_id)->count();

		if($exist == 1){ //verificando la existencia del producto
			$ingredients = $product->ingredients;
			if($ingredients->contains($ingredient_id) ){

				$product->ingredients()->detach($ingredient_id);
				$data['message'] = 'Operación realizada con éxito';
				$data['error'] = false;

			}else{

				$data['message'] = 'Error al procesar operación';
				$data['error'] = true;

			}

		}else{

			$data['message'] = 'Error al procesar operación';
			$data['error'] = true;

		}
		return $data;

	}

	/**
	 * Show product conditions.
	 * GET /{restslug}/{prodslug}/conditions
	 *
	 * @param  string  $restslug
	 * @param  string  $prodslug
	 * @return Response
	 */
	public function showconditions($restslug,$prodslug)
	{
		$restaurant = ParentRestaurant::getParent($restslug);
		$product = $restaurant->products()->where('slug',$prodslug)->firstOrFail();

		return $product->with('conditions')->get();

	}

	/**
	 * addproduct conditions.
	 * POST /{restslug}/{prodslug}/conditions/{id}
	 *
	 * @param  string  $restslug
	 * @param  string  $prodslug
	 * @param  integer $id
	 * @return Response
	 */
	public function addconditions($restslug,$prodslug,$id)
	{
		$restaurant = ParentRestaurant::getParent($restslug);
		$product = $restaurant->products()->where('slug',$prodslug)->firstOrFail();
		$condition = Condition::find($id);
		$product->conditions()->attach($condition);
		return $product->with('conditions')->get();
	}

	/**
	 * delete product conditions.
	 * POST /{restslug}/{prodslug}/conditions/{id}/delete
	 *
	 * @param  string  $restslug
	 * @param  string  $prodslug
	 * @return Response
	 */

	public function deleteconditions($restslug,$prodslug,$id)
	{
		$restaurant = ParentRestaurant::getParent($restslug);
		$product = $restaurant->products()->where('slug',$prodslug)->firstOrFail();
		$condition = Condition::find($id);

		$product->conditions()->detach($condition);

		return $product->with('conditions')->get();

	} 

	/** 
	 * Show product tags.
	 * GET /{prodslug}/tags
	 *
	 * @param  string  $restslug
	 * @param  string  $prodslug
	 * @return Response
	 */

	public function showtags($restslug,$prodslug)
	{
		$restaurant = ParentRestaurant::getParent($restslug);
		$product = $restaurant->products()->where('slug',$prodslug)->firstOrFail();
		return $product->with('tags')->get();
	}

	/** 
	 * Add product tags.
	 * POST /{prodslug}/tags/{id}
	 *
	 * @param  string  $restslug
	 * @param  string  $prodslug
	 * @return Response
	 */

	public function addtags($restslug,$prodslug,$id)
	{
		$restaurant = ParentRestaurant::getParent($restslug);
		$product = $restaurant->products()->where('slug',$prodslug)->firstOrFail();
		$condition = Tag::find($id);
		$product->tags()->attach($condition);
		return $product->with('tags')->get();
	}

	/** 
	 * Add product tags.
	 * POST /{prodslug}/tags/{id}/delete
	 *
	 * @param  string  $restslug
	 * @param  string  $prodslug
	 * @param  integer $id
	 * @return Response
	 */

	public function deletetags($restslug,$prodslug,$id)
	{
		$restaurant = ParentRestaurant::getParent($restslug);
		$product = $restaurant->products()->where('slug',$prodslug)->firstOrFail();
		$condition = Tag::find($id);
		$product->tags()->detach($condition);
		return $product->with('tags')->get();
	}
}