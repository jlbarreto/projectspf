<?php

class CartController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /cart
	 *
	 * @return Response
	 */
	public function index()
	{	
		if (Session::has('cart')){
			$cart = Session::get('cart');
			$rid = key($cart);
			$restaurant = Restaurant::find($rid);
		}else{
			$cart = null;
			$restaurant = null;
		}

		return View::make('web.cart')
			->with('cart', $cart)
			->with('restaurant', $restaurant);
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /cart/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /cart
	 *
	 * @return Response
	 */
	public function add()
	{
		$input = Input::all();
		
		try {
			$key = '';

			$product = Product::where('product_id',$input['product_id'])->with('section')->first();
			//return Response::json($product, 200);
			if(count($product) > 0){
				$restaurant = Restaurant::find($product->section->restaurant_id);
				$restaurant_id = $restaurant->restaurant_id; 
				
				$optids = ''; $ingids = '';
				
				$pconditions = array(); 
				if(isset($input['condition']) && count($input['condition']) > 0){					
					foreach ($input['condition'] as $key => $value) {
						$condition 	= Condition::find($key);
						$option 	= ConditionOption::find($value);
						$optids .= $option->condition_option_id;
						$pconditions[] = array(
							"condition_id" 			=> $condition->condition_id,
						    "condition_condition" 	=> $condition->condition,
						    "condition_option_id" 	=> $option->condition_option_id,
						    "condition_option"		=> $option->condition_option
						);
					}
				}

				$pingredients = array();
				if(isset($input['ingredients']) && count($input['ingredients']) > 0){
					foreach ($input['ingredients'] as $key => $value) {
						$ingredient = Ingredient::find($key);
						
						if($value == 0){
							$ingids .= $ingredient->ingredient_id;
						}

						$pingredients[] = array(
							"ingredient_id" => $ingredient->ingredient_id,
							"ingredient" 	=> $ingredient->ingredient,
							"active" 		=> $value
						);
					}
				}

				$key = $product->product_id . $optids . $ingids;

				if (Session::has('cart')){
					$cart = Session::get('cart');
					if(is_array($cart)){
						if(array_key_exists($restaurant_id, $cart)){
							$products = $cart[$restaurant_id];
							
							if(array_key_exists($key, $products)){

								$qty = $products[$key]['quantity'] + $input['quantity'];
								$amount = round($qty*$product->value, 2);
								$cart_row = array(
									"quantity"	  	=> $qty,
									"total_price" 	=> $amount
								);
								$cart_row = array_merge($products[$key], $cart_row);
								$cart_row = array($key => $cart_row);
								$cart[$restaurant_id] = array_replace($cart[$restaurant_id], $cart_row);
								Session::put('cart', $cart);
								$response = array('message_success' => 'Su producto ha sido agregado exitosamente al carrito, '.
										'para completar su orden dir&iacute;jase al "Carrito de compras" e inicie el proceso de pago.');
								return Response::json($response, 200);
							}else{
								$cart_row = array(
									"product_id"  	=> $product->product_id,
									"product" 		=> $product->product,
									"description"	=> $product->description,
									"conditions"	=> $pconditions,
									"ingredients"	=> $pingredients,
									"quantity"	  	=> $input['quantity'],
									"comment"	  	=> $input['comment'],
									"unit_price"  	=> $product->value,
									"total_price" 	=> round($input['quantity']*$product->value, 2)
								);

								$cart[$restaurant_id] = array_add($cart[$restaurant_id], $key, $cart_row);
								Session::put('cart', $cart);
								$response = array('message_success' => 'Su producto ha sido agregado exitosamente al carrito, '.
									'para completar su orden dir&iacute;jase al "Carrito de compras" e inicie el proceso de pago.');
								return Response::json($response, 200);	
							}							
						
						}else{
							$response = array('message_error' => 'Debe completar la orden pendiente para ordenar en este restaurante. Dirigase al "Carrito de compras" e inicie el proceso de pago.');
							return Response::json($response, 200);
						}
					}
				}else{
					$cart_row = array();
					$cart_row[$restaurant_id] = array(
						$key => 
						array(
							"product_id" 	=> $product->product_id,
							"product" 		=> $product->product,
							"description"	=> $product->description,
							"conditions" 	=> $pconditions,
							"ingredients"	=> $pingredients,
							"quantity"	 	=> $input['quantity'],
							"comment"	 	=> $input['comment'],
							"unit_price" 	=> $product->value,
							"total_price"	=> round($input['quantity']*$product->value, 2)
						)
					);
					$cart_row['name'] = $restaurant->name;
					Session::put('cart', $cart_row);
					$response = array('message_success' => 'Su producto ha sido agregado exitosamente al carrito, '.
						'para completar su orden dir&iacute;jase al "Carrito de compras" e inicie el proceso de pago.');
					
					return Response::json($response, 200);				
				}
			}else{
				$response = array('message_error' => 'El producto que esta intentando agregar no existe!');
				return Response::json($response, 200);
			}			
		} catch (Exception $e) {
			return Response::json(array('message_error' => $e->getMessage()), 400);
		}
	}

	/**
	 * Display the specified resource.
	 * GET /cart/{id}
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
	 * GET /cart/{id}/edit
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
	 * PUT /cart/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update()
	{
		$input = Input::all();
		
		try {
			// comment, quantity, array_id, product_id
			if(!empty($input['product_id'])){
				if($input['quantity'] > 0 && $input['quantity'] <= 10){
					$product = Product::find($input['product_id']);
					if (Session::has('cart')){
						$cart = Session::get('cart');
						if(is_array($cart)){
							$key = $input['key'];
							reset($cart);
							$products = current($cart);
							$restaurant_id = key($cart);
							
							if(array_key_exists($key, $products)){
								$qty = $input['quantity'];
								$amount = round($qty*$product->value, 2);
								$cart_row = array(
									"quantity"	  	=> $qty,
									// "comment"	  	=> $input['comment'],
									"total_price" 	=> $amount
								);
								$cart_row = array_merge($products[$key], $cart_row);
								$cart_row = array($key => $cart_row);
								$cart[$restaurant_id] = array_replace($cart[$restaurant_id], $cart_row);
								Session::put('cart', $cart);
								$response = array(
									"arr_key" => $key,
									"total_price" => $amount
								);
								return Response::json($response, 200);
							}else{
								return Response::json(array('message_error' => 'Producto invalido!'), 400);
							}
						}
					}
				}else{
					return Response::json(array('message_error' => 'Debe seleccionar una cantidad valida'), 400);
				}
			}else{
				return Response::json(array('message_error' => 'Debe seleccionar una cantidad valida'), 400);
			}

		} catch (Exception $e) {
			return Response::json(array('message_error' => $e->getMessage()), 400);
		}
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /cart/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function delete()
	{
		$input = Input::all();
		try {
			if(isset($input['key']) && !empty($input['key'])){
				$key = $input['key'];
				if (Session::has('cart')){
					$cart = Session::get('cart');
					reset($cart);
					$products = current($cart);
					$restaurant_id = key($cart);
					unset($products[$key]);
					$cart[$restaurant_id] = $products;
					Session::put('cart', $cart);

					$cart = Session::get('cart');
					$pds = current($cart);
					if(empty($pds)){
						Session::forget('cart');
					}
					
					return Redirect::action('CartController@index');
				}
			}
		} catch (Exception $e) {
			return Response::json(array('message_error' => $e->getMessage()), 400);
		}
	}

	/**
	 * Destroy cart.
	 * DELETE /cart/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy()
	{
		try {
			if (Session::has('cart')){
				Session::forget('cart');
				//$response = array("message_success" => "Todos los productos eliminados.");
				return Redirect::action('CartController@index');
			}
		} catch (Exception $e) {
			return Response::json(array('message_error' => $e->getMessage()), 400);
		}
	}

	public function generateKey()
	{
		/*if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		    $ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
		    $ip = $_SERVER['REMOTE_ADDR'];
		}*/
		//$ip = str_replace('.', '', $ip);
		$mtime = microtime(true);
		$mtime = str_replace('.', '', $mtime);
		$key = $mtime; //.$ip;
		
		return $key;
	}

	public function checkout(){
		try {
			if (Session::has('cart')){
				$cart = Session::get('cart');
				reset($cart);
				$restaurant_id = key($cart);

				$res_addresses = Restaurant::where("parent_restaurant_id", $restaurant_id)->get();
				foreach ($res_addresses as $key => $value) {
					$resadds[$value->restaurant_id] = $value->name." - ".$value->address; 
				}

				$sches = Schedule::where("restaurant_id", $restaurant_id)
					->where("day_id", date('N'))->get();
								
				$addrss = Address::where('user_id', Auth::user()->user_id)->get();
				foreach ($addrss as $key => $value) {
					$usradds[$value->address_id] = $value->address_name ." - ". $value->address_1 .
						(!empty($value->address_2) ? ", ".$value->address_2 : "") .
						(!empty($value->city) ? ", ".$value->city : "");
				}

				if(!isset($usradds)){
					$usradds = array ();
				}
				if(!isset($resadds) || count($resadds) == 0){
					$resadds = array ();	
				}

				return View::make('web.checkout')
					->with('res_address', $resadds)
					->with('usr_address', $usradds)
					->with('schedule', $sches);
			}else{
				return View::make('web.home');
			}
		} catch (Exception $e) {
			return Response::json(array('message_error' => $e->getMessage()), 400);
		}
		
	}
	
}