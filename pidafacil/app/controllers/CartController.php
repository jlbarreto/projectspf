<?php

class CartController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /cart
	 *
	 * @return Response
	 */
	public function index(){
		if (Session::has('cart')){
			$cart = Session::get('cart');
			$rid = key($cart);
			$restaurant = Restaurant::find($rid);
		}else{
			$cart = null;
			$restaurant = null;
		}

		$date = new DateTime();
		$fecha = $date->format('Y-m-d H:i:s');
		$promociones = Product::where('activate',1)
		               	->where('promotion',1)
		               	->where('res_products.start_date', '<=', $fecha)
				    	->where('res_products.end_date', '>=', $fecha)
				    	->count();

		return View::make('web.cart')
			->with('cart', $cart)
			->with('restaurant', $restaurant)
			->with('promociones', $promociones);
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /cart/create
	 *
	 * @return Response
	 */
	public function create(){
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /cart
	 *
	 * @return Response
	 */
	public function add(){
		$input = Input::all();
		Log::info($input);
		
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
					$numero = Session::get('cart2');
					if(is_array($cart)){
						if(array_key_exists($restaurant_id, $cart)){
							$products = $cart[$restaurant_id];
							
							if(array_key_exists($key, $products)){

								$qty = $products[$key]['quantity'] + $input['quantity'];
								$cantidadF = $numero + $qty;
								$amount = round($qty * $product->value, 2);
								$cart_row = array(
									"quantity"	  	=> $qty,
									"total_price" 	=> $amount
								);
								
								$cart_row = array_merge($products[$key], $cart_row);
								$cart_row = array($key => $cart_row);
								$cart[$restaurant_id] = array_replace($cart[$restaurant_id], $cart_row);
								
								Session::put('cart', $cart);
								Session::put('cart2', $cantidadF);
								
								$response = array('message_success' => 'Has agregado '.$product->product.' a tu Carrito de Compras.');
								return Response::json($response, 200);
							}else{
								$x = '';
								$numero = Session::get('cart2');
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
								$x = $numero + $input['quantity'];
								$cart[$restaurant_id] = array_add($cart[$restaurant_id], $key, $cart_row);
								
								Session::put('cart', $cart);
								Session::put('cart2', $x);
								Session::put('idR', $restaurant_id);
								
								$response = array('message_success' => 'Has agregado '.$product->product.' a tu Carrito de Compras.');
								return Response::json($response, 200);
							}
						}else{

							Session::forget('cart');
							Session::forget('cart2');
							$cart_row = array();
							$cart_row[$restaurant_id] = array(
								$key => array(
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
							Session::put('cart2', $input['quantity']);
							Session::put('idR', $restaurant_id);
							Log::info('Carrito: '.Session::get('cart'));
							
							$response = array('message_success' => 'Has agregado '.$product->product.' a tu Carrito de Compras.');
							return Response::json($response, 200);
						}
					}
				}else{
					$numero2 = Session::get('cart2');
					$cart_row = array();
					$cart_row[$restaurant_id] = array(
						$key => array(
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
					
					$suma = $numero2 + $input['quantity'];
					$cart_row['name'] = $restaurant->name;
					
					Session::put('cart', $cart_row);
					Session::put('cart2', $suma);
					Session::put('idR', $restaurant_id);
					
					$response = array('message_success' => 'Has agregado '.$product->product.' a tu Carrito de Compras.');
					
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
	public function show($id){
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /cart/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id){
		//
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /cart/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update(){
		$input = Input::all();
		
		try {
			// comment, quantity, array_id, product_id
			if(!empty($input['product_id'])){
				if($input['quantity'] > 0 && $input['quantity'] <= 10){
					$valor = $input['quantity'];
					$product = Product::find($input['product_id']);
					if (Session::has('cart')){
						$cart = Session::get('cart');
						$var2 = Session::get('cart2');
						
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
								Session::put('cart2', $var2+$qty);
					
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
	public function delete(){
		$input = Input::all();
		try {
			if(isset($input['key']) && !empty($input['key'])){
				$key = $input['key'];
				if (Session::has('cart')){
					$cart = Session::get('cart');
					$numP = Session::get('cart2');
					reset($cart);
					$products = current($cart);
					$restaurant_id = key($cart);
					$qty2 = $products[$key]['quantity'];
					unset($products[$key]);
					$cart[$restaurant_id] = $products;

					$newCant = $numP - $qty2;

					Session::put('cart', $cart);
					Session::put('cart2', $newCant);

					$cart = Session::get('cart');
					$pds = current($cart);
					if(empty($pds)){
						Session::forget('cart');
						Session::forget('cart2');
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
	public function destroy(){
		try {
			if(Session::has('cart')){
				Session::forget('cart');
				Session::forget('cart2');
				//$response = array("message_success" => "Todos los productos eliminados.");
				return Redirect::action('CartController@index');
			}
		}catch(Exception $e){
			return Response::json(array('message_error' => $e->getMessage()), 400);
		}
	}

	public function generateKey(){
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
		try{
			if (Session::has('cart')){
				$cart = Session::get('cart');
				reset($cart);
				$restaurant_id = key($cart);
                $idParen= DB::table('res_restaurants')
                    ->select('parent_restaurant_id')
                    ->where('restaurant_id', $restaurant_id)
                    ->get();
				
				$res_addresses = Restaurant::where("parent_restaurant_id", $idParen[0]->parent_restaurant_id)->where("activate",1)->get();

                $shipping_charge = 0;
                
                foreach ($res_addresses as $key => $value) {
					$resadds[$value->restaurant_id] = $value->name." - ".$value->address;
                        
                    if($value->restaurant_id==$value->parent_restaurant_id){
                        //Si es el padre usar ese costo de envío
                        $shipping_charge = $value->shipping_cost;
                    }
				}

				$sches = Schedule::where("restaurant_id", $restaurant_id)
					->where("day_id", date('w')+1)->get();
								
				$addrss = Address::where('user_id', Auth::user()->user_id)->orderBy('address_id', 'DESC')->get();
				foreach($addrss as $key => $value){
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

                if(count($resadds)>1){
                    foreach ($resadds as $k=>$v) {
                        if($k==$idParen[0]->parent_restaurant_id){
                            unset($resadds[$k]);
                        }
                    }
                }
                
                $data['contador_add'] = count($addrss);
                $data['res_address']=$resadds;
                $data['usr_address']=$usradds;
                
                //Devolviendo la id, del padre
                $data['restaurant_id']=$restaurant_id;
                $data['parent_shipping_cost']=$shipping_charge;
                $data['schedule']=$sches;
                
                //For new Addresses
                $statelst = State::where('country_id', 69)->where('active', true)->get();
                
                $data['states'] = array();
                $data['municipalities'] = array();
                $data['zones'] = array();
                $data['states']['']="--Seleccione un departamento--";
                $data['municipalities']['']="--Seleccione un municipio--";
                $data['zones']['']="--Seleccione una zona--";

                //Obtengo todas las zonas sin necesidad de los municipios
                $zonas = Zone::select('zone_id', DB::raw('CONCAT(zone, " - $", price) AS name_zona'))
						    ->join('pf.com_shipping_prices', 'pf.com_zones.shipping_price_id', '=', 'pf.com_shipping_prices.shipping_price_id')
						    ->lists('name_zona', 'zone_id');
                
                #sort($zonas);
		        $combobox = array('none' => "--Seleccione una zona") + $zonas;
		        $selected = array();
                                
                foreach ($statelst as $state) {
                    $data['states'][$state->state_id] = $state->state;
                }

                $date = new DateTime();
		        $fecha = $date->format('Y-m-d H:i:s');

		        $promociones = Product::where('activate',1)
						        ->where('promotion',1)
						        ->where('res_products.start_date', '<=', $fecha)->where('res_products.end_date', '>=', $fecha)
						        ->count();

				return View::make('web.checkout', compact('combobox', 'selected'))
					->with('promociones', $promociones)
					->with($data);
			}else{
				return View::make('web.home');
			}
		} catch (Exception $e) {
			return Response::json(array('message_error' => $e->getMessage()), 400);
		}
	}

	public function bines(){

		$promoActiva = ConfigBin::where('activate',1)->get();
		if($promoActiva->count() != 1){
			return 0;

		} else {
			return Response::json(array('error' => false, 'discount' => true,'porcentaje' => $promoActiva->first()->porcentaje ));
			$input = (object) Input::all();
			$credit_card = str_replace("-","",$input->credit);
			$credit_card = str_replace(" ","",$credit_card);

			$bin = substr($credit_card, 0, 6);
			if(ListBin::where('num_bin',$bin)->count() > 0){
				 
				return Response::json(array('error' => false, 'discount' => true,'porcentaje' => $promoActiva->first()->porcentaje ));


			} else{
				return Response::json(array('error' => 'false', 'discount' => false));

			}
			return ;
		}
	}
}
