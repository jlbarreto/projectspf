<?php

class ProductController extends \BaseController {

	/**
	* Display a listing of the resource.
	* GET /product
	*
	* @return Response
	*/
	public function index()
	{
		//
	}

	/**
	* Show the form for creating a new resource.
	* GET /product/create
	*
	* @return Response
	*/
	public function create()
	{
		//
	}

	/**
	* Store a newly created resource in storage.
	* POST /product
	*
	* @return Response
	*/
	public function store()
	{
		//
	}

	/**
	* Display the specified resource.
	* GET /product/{id}
	*
	* @param  int  $id
	* @return Response
	*/
	public function show()
	{
		//
		$input = Input::all();
		try {
			$statusCode = 200;
			$products = Product::where('product_id', $input['product_id'])->where('activate', true)->firstOrFail();
			$products['conditions']  = $products->conditions()
			->with(['options'=>function($query){
				$query->where('active', 1);
			}])->get();
			$products['ingredients'] = $products->ingredients()
																					->where('active', 1)
																					->where('removable', 1)
																					->orderBy('position', 'asc')
																					->get();

			//Obteniendo datos del restaurante
			$section = $products->section()->get()[0];
			$r = $section->restaurant()->get();

			if($r[0]->restaurant_id==$r[0]->parent_restaurant_id){
				$web_content = $r[0]->landingPage()->firstOrFail();
			}else{
				$web_content = Restaurant::findOrFail($r[0]->parent_restaurant_id)->landingPage()->firstOrFail();
			}

			$products['restaurant_id']=$r[0]->restaurant_id;
			$products['restaurant_name']=$r[0]->name;
			$products['restaurant_logo']=$web_content->logo;


			if (!empty($products)) {
				$response['status'] = true;
				$response['data'] = $products;
			} else {
				$response = array(
					"status" => false,
					"data" => 'No existe el producto solicitado.'
				);
			}
		} catch (Exception $e) {
			$statusCode = 400;
			$response = array(
				"status" => false,
				"data" => $e->getMessage()
			);
		}
		return Response::json($response, $statusCode);
	}

	/**
	* Show the form for editing the specified resource.
	* GET /product/{id}/edit
	*
	* @param  int  $id
	* @return Response
	*/
	public function edit($id)
	{
		//
	}

	/**
	* Update the specified resource in storage.
	* PUT /product/{id}
	*
	* @param  int  $id
	* @return Response
	*/
	public function update($id)
	{
		//
	}

	/**
	* Remove the specified resource from storage.
	* DELETE /product/{id}
	*
	* @param  int  $id
	* @return Response
	*/
	public function destroy($id)
	{
		//
	}

	/**
	* Display a listing of the resource by promos.
	* GET /products/promos
	*
	* @return Response
	*/
	public function promos()
	{
		// Show  promos by restaurant
		// $pagesze, $pagepos
		$input = Input::all();
		$statusCode = 200;
		$date = date('Y-m-d H:i:s');
		try {
			if(isset($input['page_size']) && $input['page_size'] > 0){
				$pagesze = $input['page_size'];
				$pagepos = $input['page_post'];
				$products = Restaurant::find($input['restaurant_id'])
				->products()->where('res_products.activate', true)
				->where('promotion', true)
				->where('start_date', '<=', $date)
				->where('end_date', '>=', $date)
				->take($pagesze)->skip($pagepos * $pagesze)
				->get();
			}else{
				$products = Restaurant::find($input['restaurant_id'])
				->products()->where('res_products.activate', true)
				->where('promotion', true)
				->where('start_date', '<=', $date)
				->where('end_date', '>=', $date)
				->get();
			}

			if ($products->count() > 0) {
				$response['status'] = true;
				$response['data'] = $products;
			} else {
				$response = array(
					"status" => false,
					"data" => 'No hay promociones en el restaurante.'
				);

			}
		} catch (Exception $e) {
			$statusCode = 400;
			$response = array(
				"status" => false,
				"data" => $e->getMessage()
			);
		}
		return Response::json($response, $statusCode);
	}

	public function restaurant() {
		$input = Input::all();
		$statusCode = 200;
		try {
			$product = Product::findOrFail($input['product_id']);
			$section = $product->section()->get()[0];
			$r = $section->restaurant()->get();

			if (count($r) > 0) {
				if($r[0]->restaurant_id==$r[0]->parent_restaurant_id){
					$web_content = $r[0]->landingPage()->firstOrFail();
				}else{
					$web_content = Restaurant::findOrFail($r[0]->parent_restaurant_id)->landingPage()->firstOrFail();
				}

				$response['status'] = true;
				$response['data'] =
				array(
					'restaurant_id'=>$r[0]->restaurant_id,
					'name'=>$r[0]->name,
					'restaurant_logo'=>$web_content->logo,
					'product_id'=>$product->product_id,
					'product_name'=>$product->product
				);
			} else {
				$response = array(
					"status" => false,
					"data" => 'No se pudo recuperar el restaurant.'
				);
			}
		} catch (Exception $e) {
			$statusCode = 400;
			$response = array(
				"status" => false,
				"data" => $e->getMessage()
			);
		}
		return Response::json($response, $statusCode);
	}
}
