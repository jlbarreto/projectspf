<?php

class OrderController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		// $orders = User::find(Auth::id())->orders;
		// $orders = Order::users()
		$orders = Order::join('req_order_ratings', function($join)
	        {
	            $join->on('req_orders.order_id', '=', 'req_order_ratings.order_id')
	                 ->where('req_order_ratings.user_id', '=', Auth::id());
	        })
			->with('statusLogs')
			->orderBy('created_at', 'desc')
			->groupBy('req_orders.order_id')
		->get();
		return View::make('web.user_orders')->with('orders', $orders);
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */

    //Genera numero de orden de manera aleatoria
    public function numeroOrden($length=0,$uc=FALSE,$n=TRUE,$sc=FALSE)
    {
        //se consulta la Hora y se le agrega al final para evitar que se repita el numero
        $hora = date("G");
        //se compara la cantidad de digitos para colocar el numero de digitos en length
        if(strlen($hora)==2)
        {
            $length = 4;
        }else{
            $length = 5;
        }
        $source = 'ABCDEFGHJKMNPQRSTUVWXYZ';
        if($uc==1) $source .= 'abcdefghjkmnpqrstuvwxyz';
        if($n==1) $source .= '23456789';
        if($sc==1) $source .= '|@#~$%()=^*+[]{}-_';
        if($length>0){
            $rstr = "";
            $source = str_split($source,1);
            for($i=1; $i<=$length; $i++){
                mt_srand((double)microtime() * 1000000);
                $num = mt_rand(1,count($source));
                $rstr .= $source[$num-1];
            }

        }
        $rstr = $rstr.$hora;

        return $rstr;
    }

	public function store()
	{
		$rules = array(
			'service_type_id' 	=> 'required|numeric',
			'payment_method_id' => 'required|numeric'
		);
	    $display = array(	    	
			'service_type_id'	=> 'Tipo de servicio',
			'payment_method_id' => 'M&eacute;todo de pago'
		);
	   	//Rules before defined
		$validator = Validator::make(Input::all(), $rules);
		//Field name translation
		$validator->setAttributeNames($display);

		if ($validator->fails()) {
			return Redirect::to('cart/checkout')
            	->withErrors($validator)
            	->withInput();
		}else{
			$irules = array();
			$idisplay = array();

			$st_id = Input::get('service_type_id');
			if($st_id == 1){
				$irules = array_add($irules, 'address_id', 'required|numeric');
				$idisplay = array_add($idisplay, 'address_id', 'Direcci&oacute;n de env&iacute;o');
			}elseif($st_id == 2){
				$irules = array_add($irules, 'restaurant_address', 'required|numeric');
				$idisplay = array_add($idisplay, 'restaurant_address', 'Direcci&oacute;n de sucursal');
			}

			$pm_id = Input::get('payment_method_id');
			if($pm_id == 1){
				$irules = array_add($irules, 'cash', 'required');
				$idisplay = array_add($idisplay, 'cash', 'Efectivo');
			}elseif($pm_id == 2){
				$irules = array_add($irules, 'user_credit', 'required|min:6');
				$irules = array_add($irules, 'credit_card', 'required|luhn');
				$irules = array_add($irules, 'month', 		'required|numeric|min:1');
				$irules = array_add($irules, 'year', 		'required|numeric|min:1');
				$irules = array_add($irules, 'secure_code', 'required|min:3');
				
				$idisplay = array_add($idisplay, 'user_credit', 'Nombre del titular');
				$idisplay = array_add($idisplay, 'credit_card', 'N&uacute;mero de la tarjeta');
				$idisplay = array_add($idisplay, 'month', 'Mes de expiraci&oacute;n');
				$idisplay = array_add($idisplay, 'year', 'A&ntilde;o de expiraci&oacute;n');
				$idisplay = array_add($idisplay, 'secure_code', 'C&oacute;digo de seguridad');
			}

			//Rules before defined
			$ivalidator = Validator::make(Input::all(), $irules);
			//Field name translation
			$ivalidator->setAttributeNames($idisplay);

			if ($ivalidator->fails()) {
				$messages = $ivalidator->messages();
				return Redirect::to('cart/checkout')
	            	->withErrors($ivalidator)
	            	->withInput(Input::except('credit_card', 'secure_code'));
			}else{
				// return Response::json(array('message'=>'Guardar orden'), 200);
				$idusuario = Auth::id();
				if (Session::has('cart')){
					$cart = Session::get('cart');
				}
				else{
					return Redirect::to('cart');
				}

			/* ############################################################## */
				if(is_array($cart)){
					foreach($cart as $req_restaurant => $req_order){
                        $idRes = DB::Table('res_restaurants')
                            ->select('parent_restaurant_id')
                            ->where('restaurant_id', $req_restaurant)
                            ->get();
						//------------- Se comienza la orden -------------------
						if(!empty($idusuario) && $req_restaurant != 'name' && is_numeric($req_restaurant)){
							//verificamos si corresponde al id de restaurante
							
							//realizamos conteo de direcciones
							$nuevaorden = new Order; //creamos una nueva orden
							$service_type_id = Input::get('service_type_id');
							if(!empty($service_type_id)){ //verificando el parametro de service type
								$service_type = ServiceType::findOrFail($service_type_id);
								$nuevaorden->service_type_id = $service_type->service_type_id;
								if($service_type->service_type_id == 1 || $service_type->service_type_id == 3){

                                    $nuevaorden->restaurant_id = $idRes[0]->parent_restaurant_id;
									$count = Address::where('user_id',$idusuario)->count();
									if($count ==1) {
										//si existe solo una dirección se trabajará con ella
										$direccion = Address::where('user_id',$idusuario)->firstOrFail();	
										$addr = $direccion->address_name . ' - ' .  $direccion->address_1 . 
											(!empty($direccion->address_2) ? ', '.$direccion->address_2 : '') .
											', ' . $direccion->city . 
											', ' . $direccion->state;

										$nuevaorden->address = $addr;
									}else{
										//Si esxiste más de una dirección se espera parámetro para decidir con cual trabajará
										$address_id = Input::get('address_id');
										if(!empty($address_id)){
											$direccion = Address::where('user_id',$idusuario)->where('address_id', $address_id)->firstOrFail();	
											$addr = $direccion->address_name . ' - ' .  $direccion->address_1 . 
											(!empty($direccion->address_2) ? ', '.$direccion->address_2 : '') .
											', ' . $direccion->city . 
											', ' . $direccion->state;

											$nuevaorden->address = $addr;
										}else
											return 'Falta especificar la dirección'; //Falta mandar el parametro	
									}
								}elseif($service_type->service_type_id == 2){
									$restaurant_address = Input::get('restaurant_address');
                                    $nuevaorden->restaurant_id = Input::get('restaurant_address');
									if(!empty($restaurant_address)){
										$direccion = Restaurant::where('restaurant_id', $restaurant_address)->firstOrFail();
										$addr = $direccion->name . ' - ' .  $direccion->address;

										$nuevaorden->address = $addr;
										$nuevaorden->pickup_hour = Input::get('hour');								
										$nuevaorden->pickup_min = Input::get('minutes');
									}else
										return 'Falta especificar la dirección'; //Falta mandar el parametro
								}
							}else
								return 'No existe tipo de servicio';

							$payment_method_id = Input::get('payment_method_id');	
							if(!empty($payment_method_id)){//verificando el campo payment_method
								$payment_method = PaymentMethod::findOrFail($payment_method_id);
								$nuevaorden->payment_method_id = $payment_method->payment_method_id;
								if($payment_method->payment_method_id == 2){
									$nuevaorden->credit_name = Input::get('user_credit');
									$nuevaorden->credit_card = Input::get('credit_card');
									$nuevaorden->credit_expmonth = Input::get('month');
									$nuevaorden->credit_expyear = Input::get('year');							
									$nuevaorden->secure_code = Input::get('secure_code');
								}elseif($payment_method->payment_method_id == 1){
									$nuevaorden->pay_bill = Input::get('cash');
								}else
									return 'No existe tipo de pago';
							}else
								return 'No existe tipo de pago';
							 // $nuevaorden->order_status_id = 1;
							//  $nuevaorden->restaurant_id = $idRes[0]->parent_restaurant_id;
							$nuevaorden->save();// Se guarda la orden padre

							// Asignar el primer estado a la orden
							$order_stat = new OrderStatusLog;
							$order_stat->order_id = $nuevaorden->order_id;
							$order_stat->user_id = $idusuario;
							$order_stat->order_status_id = 1;
							$order_stat->save();
							
							// Asignar la orden al usuario
							$order_usr = new OrderRating;
							$order_usr->order_id = $nuevaorden->order_id;
							$order_usr->user_id = $idusuario;
							$order_usr->save();
						}

						if(is_array($req_order)){
							foreach ($req_order as $key => $req_product) {
								// Se comienza el detail de de la orden
								$detalle_producto = new OrderDetail;
								$detalle_producto->order_id = $nuevaorden->order_id;
								$detalle_producto->product_id = $req_product['product_id'];
								$detalle_producto->quantity = $req_product['quantity'];
								$detalle_producto->product = $req_product['product'];
								$detalle_producto->unit_price = $req_product['unit_price'];
								$detalle_producto->total_price = $req_product['total_price'];
								$detalle_producto->comment = $req_product['comment'];
								$detalle_producto->save();
								$product = Product::findOrFail($detalle_producto->product_id);
								$nuevo_total = ($product->value * $detalle_producto->quantity) + $nuevaorden->order_total;
								$nuevaorden->order_total = $nuevo_total;
                                $nuevaorden->order_cod = $this->numeroOrden();
								$nuevaorden->save();//Se hace update de precio final
								if(!empty($req_product['ingredients'])){
									foreach($req_product['ingredients'] as $ingrediente){
										//Verificando los ingredientes de la orden
										$detalle_ingrediente = new OrderDetailProductIngredient;
										$detalle_ingrediente->order_det_id = $detalle_producto->order_det_id;
										$ingredient = Ingredient::findOrFail($ingrediente['ingredient_id']);
										$detalle_ingrediente->ingredient_id = $ingredient->ingredient_id;
										$detalle_ingrediente->ingredient = $ingredient->ingredient;
										if($ingrediente['active'] == 1){
											$detalle_ingrediente->remove = 0;
										}else{
											$detalle_ingrediente->remove = 1;
										}

										$detalle_ingrediente->save();
									}
								}
								if(!empty($req_product['conditions'])){
									// verificando las condiciones que hay en el producto
									foreach($req_product['conditions'] as $condition){
										$detalle_opcion = new OrderDetailProductCondition;
										$detalle_opcion->order_det_id = $detalle_producto->order_det_id;
										$detalle_opcion->condition_id = $condition['condition_id'];
										$detalle_opcion->condition = $condition['condition_condition'];
										$detalle_opcion->condition_option_id = $condition['condition_option_id'];								
										$detalle_opcion->condition_option = $condition['condition_option'];
										$detalle_opcion->save();
									}
								}		
							}

							if($nuevaorden->pay_bill > 0){
								$nuevaorden->pay_change = $nuevaorden->pay_bill - $nuevaorden->order_total;
								$nuevaorden->save();
							}

						}
					}
				}
				$response['status'] = true;
				$response['data'] = $nuevaorden;
				Session::forget('cart');
				Session::forget('total_order');
				return Redirect::to('user/orders');

			/* ############################################################## */

			}

		}
		
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function lista($restslug)
	{
		if($restslug =='all'){
			return Order::orderBy('created_at', 'desc')->get();

		}else{


			$restaurante = Restaurant::where('slug',$restslug)->firstOrFail();
			return $restaurante->orders()->orderBy('created_at', 'desc')->get();

		}
		
	}
	public function orden($restslug,$id){
		$restaurant = Restaurant::where('slug',$restslug)->firstOrFail();
		$orden = Order::where('restaurant_id',$restaurant->restaurant_id)->where('order_id',$id)->orderBy('created_at', 'desc')->firstOrFail();
		$response['order'] = $orden;
		$response['servicetype']= $orden->serviceType()->get();
		$response['paymentMethod'] = $orden->paymentMethod()->get();
		$response['orderStatus'] = $orden->orderStatus()->get();
		return $orden->products()->get();
		return $response;



	}



	/**
	 * Show the form for editing the specified resource.
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
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update()
	{
		$input = Input::all();

		$order = Order::find($input['order_id'])->firstOrFail();
		$order->service_type_id 	= $input['service_type_id']; //NULL by default
		$order->address_id 			= $input['address_id']; //NULL by default
		$order->payment_method_id 	= $input['payment_method_id']; //NULL by default
		$order->order_status_id 	= 2;

		$order->save();

		$response['status'] = true;
		$response['data']	= $order;
		
		return $response;
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	/**
	 * Show completed orders by user.
	 *
	 * @return Response
	 */
	public function repeat(){
		$input = Input::all();
		if(!empty($input['oid']) && $input['oid'] > 0){
			$oid = $input['oid'];
			$order = Order::findOrFail($oid);	
			$user = $order->users->first();
			if($user->user_id == Auth::id()){
				Session::forget('cart');
				$cart = array();
				$restaurant_id = $order->restaurant_id; 
				$cart[$restaurant_id] = array();
				$cart['name'] = $order->restaurant->name;

				$products = $order->products;
				$optids = ''; $ingids = '';
				foreach ($products as $key => $value) {
					$prd = $value->getProduct;
					$cnds = $value->conditions;
					$ings = $value->ingredients;

					if($cnds->count() > 0){
						foreach ($cnds as $ky => $val) {
							$optids .= $val->condition_option_id;
							$pconditions[] = array(
								"condition_id" 			=> $val->condition_id,
							    "condition_condition" 	=> $val->condition,
							    "condition_option_id" 	=> $val->condition_option_id,
							    "condition_option"		=> $val->condition_option
							);
						}
					}else{
						$pconditions = NULL;
					}

					if($ings->count() > 0){
						foreach ($ings as $k => $v) {
							$ingids .= $v->ingredient_id;

							if($v->remove == 1){
								$active = 0;
							}elseif($v->remove == 0){
								$active = 1;
							}

							$pingredients[] = array(
								"ingredient_id" => $v->ingredient_id,
								"ingredient" 	=> $v->ingredient,
								"active" 		=> $active
							);
						}
					}else{
						$pingredients = NULL;
					}

					$pkey = $prd->product_id . $optids . $ingids;

					$cart_row = array(
						"product_id"  	=> $prd->product_id,
						"product" 		=> $prd->product,
						"description"	=> $prd->description,
						"conditions"	=> $pconditions,
						"ingredients"	=> $pingredients,
						"quantity"	  	=> $value->quantity,
						"comment"	  	=> $value->comment,
						"unit_price"  	=> $prd->value,
						"total_price" 	=> round($value->quantity*$prd->value, 2)
					);
					$cart[$restaurant_id] = array_add($cart[$restaurant_id], $pkey, $cart_row);
				}
				//$response = $cart;
				Session::put('cart', $cart);
/* ########################################################################## 
			$product = Product::where('product_id',$input['product_id'])->with('section')->first();
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
			}
/* ########################################################################## */

			}else{
				$response = array('message' => 'User not allowed');
			}
		}else{
			$response = array('message' => 'Order not allowed');
		}

		// $orders = Order::users()

		/*$orders = DB::table('req_order_status_logs')
        	->select(DB::raw('MAX(req_order_status_logs.order_status_id) as ultimo, req_orders.order_id'))
            ->leftJoin('req_orders', 'req_order_status_logs.order_id', '=', 'req_orders.order_id')
            ->leftJoin('req_order_ratings', 'req_orders.order_id', '=', 'req_order_ratings.order_id')
	        ->where('req_order_ratings.user_id', '=', Auth::id())
	        ->whereNotNull('req_orders.address')
	        ->having('ultimo', '=', 5)
            ->groupBy('req_orders.order_id')
            ->get();
           */

		// return Response::json($response, 200);
		return Redirect::action('CartController@index');
	}

}
