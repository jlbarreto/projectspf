<?php

class OrderController extends BaseController {

    public function asd(){
        return 'AAA';
    }

    public function allMotos(){
        $motos = DetalleMotorista::get();

        return View::make('web.listadoMotos')
            ->with('motos',$motos);
    }

	public function allRest(){
		//$restaurantes = Restaurant::where('activate', 1)->get();
		$restaurantes = DB::SELECT('SELECT * FROM pf.res_restaurants where restaurant_id = parent_restaurant_id and activate = 1');
		$landing = LandingPage::select('header','logo','landing_page_id')->get();

		return View::make('web.restaurantes')
			->with('restaurantes', $restaurantes)
			->with('landing', $landing);
	}

	public function allProducts($id_rest){

		//Session::forget('cart');
		Session::put('restaurante_id', $id_rest);

		$productos = DB::SELECT('
						SELECT * FROM pf.res_products as prod
						inner join res_sections as sec ON prod.section_id = sec.section_id
						inner join res_restaurants as rest ON sec.restaurant_id = rest.restaurant_id
						where rest.restaurant_id = ? and prod.activate = 1', array($id_rest));

		$restaurant = Restaurant::where('restaurant_id', $id_rest);

		$landing = DB::SELECT('
								SELECT * FROM pf.res_web_content as web 
								inner join res_restaurants as rest ON web.landing_page_id = rest.landing_page_id
								where rest.restaurant_id= ?', array($id_rest));
		
		return View::make('web.products_rest')
			->with('productos', $productos)
			->with('restaurant', $restaurant)
			->with('landing', $landing)
			->with('id_restaurante', $id_rest);
	}

	public function searchProduct(){		

		$palabra = Input::get('products');
		$id_rest = Session::get('restaurante_id');
		Log::info(Session::get('restaurante_id'));
		
		if(empty($palabra)){
			$productos = DB::SELECT('
						SELECT * FROM pf.res_products as prod
						inner join res_sections as sec ON prod.section_id = sec.section_id
						inner join res_restaurants as rest ON sec.restaurant_id = rest.restaurant_id
						where rest.restaurant_id = ? and prod.activate = 1', array(Session::get('restaurante_id')));

			$restaurant = Restaurant::where('restaurant_id', Session::get('restaurante_id'));

			$landing = DB::SELECT('
								SELECT * FROM pf.res_web_content as web 
								inner join res_restaurants as rest ON web.landing_page_id = rest.landing_page_id
								where rest.restaurant_id= ?', array(Session::get('restaurante_id')));

		}else{

			$productos = DB::SELECT('
							SELECT * FROM pf.res_products as prod
							inner join res_sections as sec ON prod.section_id = sec.section_id
							inner join res_restaurants as rest ON sec.restaurant_id = rest.restaurant_id
							where rest.restaurant_id = '.Session::get('restaurante_id').' and prod.activate = 1
							AND prod.product like "%'.$palabra.'%"
						');

			$restaurant = Restaurant::where('restaurant_id', Session::get('restaurante_id'));

			$landing = DB::SELECT('
								SELECT * FROM pf.res_web_content as web 
								inner join res_restaurants as rest ON web.landing_page_id = rest.landing_page_id
								where rest.restaurant_id= ?
							', array(Session::get('restaurante_id')));
		}

		//Session::forget('restaurante_id');
		
		return View::make('web.products_rest')
			->with('productos', $productos)
			->with('restaurant', $restaurant)
			->with('landing', $landing)
			->with('id_restaurante', $id_rest);
	}

	public function dataProduct(){
		if(Request::ajax()){
			$html = '';

			$restaurante_id = Input::get('restaurante_id');

			$prod_id = Input::get('producto_id');

			$products = Product::where('product_id', $prod_id)->where('activate', true)->firstOrFail();
			
			$products['conditions'] = $products->conditions()
				->with(['opciones'=>function($query){
					$query->where('active', 1);
				}])->get();
			
			$products['ingredients'] = $products->ingredients()
				->where('active', 1)
				->where('removable', 1)
				->orderBy('position', 'asc')
				->get();
				
				//<img src="http://images.pf.techmov.co'.$products->image_web.'" alt="Imagen de producto">
			
			$html .= '
					<input type="hidden" name="product_id" id="product_id" value="'.$products->product_id.'">
					<input type="hidden" name="restaurante_id" id="restaurante_id" value="'.$restaurante_id.'">
					<div class="row">
						<div class="col-md-12">
							<div class="col-md-2">
								<div class="singleproduct_image">';
								if ($products->image_web != null) {
									$html .= '<img src="http://images.pf.techmov.co'.$products->image_web.'" alt="Imagen de producto">';
								}else{
									$html .= '<img src="http://images.pf.techmov.co/no_image.jpg" alt="Imagen de producto">';
								}

								$html .= '</div>
							</div>
							<div class="col-md-1"></div>						
							<div class="col-md-6" text-align:left;>
								<h3>'.$products->product.'</h3>
								<span>'.$products->description.'</span>
								<br>
								<label>$ '.$products->value.'</label>
							</div>
							<div class="col-md-3">
								<h4>Cantidad:</h4>
								<select class="form-control valid"  id="quantity" name="quantity" aria-invalid="false">';
									for ($i = 1; $i <= 10; $i++) {
									    $html .= '<option value="'.$i.'">'.$i.'</option>';
									}
								$html .= '</select>
							</div>
						</div>
					</div>
					<hr>
					<div class="row">
						<div class="col-md-1 col-xs-2"></div>
						<div class="col-md-4 col-xs-8">
					        <h2>Condiciones</h2>
					        ';
					        if(count($products['conditions']) > 0){
					        	foreach ($products['conditions'] as $key => $value) {
					        		$options = array();
					        		$html .= '<article class="condition form-group">';
					        		foreach ($value->opciones as $k => $val) {
					        			$options[$val->condition_option_id] = $val->condition_option;
					        		}
					        		$html .= '<label for="condition">'.$value->condition.'</label>				        			
					        				<br />
					        				<select class="form-control condition" id="condition_'.$value->condition_id.'" name="condition['.$value->condition_id.']" title="Por favor seleccione una opcion" required>
					        					<option ="null">Seleccionar...</option>';
												foreach ($options as $key => $value) {
					        						$html .= '				        							
					        							<option value="'.$key.'">'.$value.'</option>
					        						';
					        					}											
					        					
					        				$html .= '</select> <br />';
					        	}
					        }
					        $html .='
					    </div>
					    <div class="col-md-1 col-xs-2"></div>
					    <div class="col-md-6 col-xs-12" style="margin:0px auto;">
					    	<h2>Ingredientes</h2>
	                        <div class="ingredients_select">';
		                        if (count($products['ingredients']) > 0) {
		                        	$html .= '<ul class="white_content">';
		                        	$ig = 0;
		                        	foreach ($products['ingredients'] as $key => $value) {
		                        		if ($value->pivot->removable == 1) {
		                        			$html .= '
		                        				<li>
			                        				<input type="hidden" name="ingredients['.$value->ingredient_id.']" value="0"/>
			                        				<div class="col-md-3">
			                        					<input type="checkbox" name="ingredients['.$value->ingredient_id.']" id="ingredient_'.$value->ingredient_id.'" value="1" checked>
			                        				</div>
			                        				<div class="col-md-9" style="text-align:left;">
			                        					<label for="ingredient_'.$value->ingredient_id.'">'.$value->ingredient.'</label>
			                        				</div>	                        				
		                        				</li>
		                        			';
		                        			$ig++;
		                        		}
		                        	}
		                        	if ($ig == 0) {
		                        		$html .= '
		                						<li>
		                    						Este producto no tiene ingredientes removibles
		                						</li>';
		                        	}
		                        }else{
		                        		$html .= '
		                        			<ul>
		                        				<li class="white_content">
		                        					<br><br><br>
		                        					<label>Este producto no tiene ingredientes removibles</label>
		                        				</li>
		                        			</ul>
		                        		';
		                        	}
	                        $html .= '</div>
					    </div>
					</div>
					<div class="row">
						<div class="col-xs-2"></div>
						<div class="col-xs-8">
							<h2>Comentario</h2>
							<p>Escriba un pequeño comentario al pedido (ej. <i>"Deseo el queso derretido"</i>).</p>
							<textarea name="comment" class="form-control" rows="3"></textarea>						
						</div>
						<div class="col-xs-2"></div>
					</div>
			';
			
			return Response::json($html);
	    }		
	}

	//Funcion para crear carrito de compras
	public function add(){
		$input = Input::all();
		Log::info($input);
		
		try {
			$key = '';

			$product = Product::where('product_id', $input['product_id'])->first();
			//return Response::json($product, 200);
			if(count($product) > 0){
				$restaurant = Restaurant::find($input['restaurante_id']);
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
								$amount = round($qty*$product->value, 2);
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
								Log::info("RESPONSE 1");
								Log::info($response);
								return Response::json($response, 200);
								//return Redirect::to('productRest/'.$restaurant_id.'');
								//return Redirect::to('cart');
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
								
								Log::info("RESPONSE 1");
								Log::info($response);
								
								return Response::json($response, 200);

								//return Redirect::to('productRest/'.$restaurant_id.'');
								//return Redirect::to('cart');
							}							
						
						}else{

							Session::forget('cart');
							Session::forget('cart2');
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
							Session::put('cart2', $input['quantity']);
							Session::put('idR', $restaurant_id);
							Log::info('Carrito: '.Session::get('cart'));
							
							$response = array('message_success' => 'Has agregado '.$product->product.' a tu Carrito de Compras.');
							Log::info("RESPONSE 3");
							Log::info($response);
							return Response::json($response, 200);
							//return Redirect::to('productRest/'.$restaurant_id.'');
							//return Redirect::to('cart');
						}
					}
				}else{
					$numero2 = Session::get('cart2');
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
					$suma = $numero2 + $input['quantity'];
					$cart_row['name'] = $restaurant->name;
					Session::put('cart', $cart_row);
					Session::put('cart2', $suma);
					Session::put('idR', $restaurant_id);

					$response = array('message_success' => 'Has agregado '.$product->product.' a tu Carrito de Compras.');
					Log::info("RESPONSE 4");
					Log::info($response);
					return Response::json($response, 200);					
					//return Redirect::to('productRest/'.$restaurant_id.'');
					//return Redirect::to('cart');
				}
			}else{
				$response = array('message_error' => 'El producto que esta intentando agregar no existe!');
				return Response::json($response, 200);
			}			
		} catch (Exception $e) {
			return Response::json(array('message_error' => $e->getMessage()), 400);
		}
	}

	public function viewCart(){
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
		/*$promociones = Product::where('activate',1)
		               	->where('promotion',1)
		               	->where('res_products.start_date', '<=', $fecha)
				    	->where('res_products.end_date', '>=', $fecha)
				    	->count();*/

		return View::make('web.cart')
			->with('cart', $cart)
			->with('restaurant', $restaurant);
			//->with('promociones', $promociones);
	}


	//Funcion para cargar vista checkout
	public function checkout(){
		try {
			if (Session::has('cart')){
				$cart = Session::get('cart');
				reset($cart);
				$restaurant_id = key($cart);
                $idParen= DB::table('res_restaurants')
                    ->select('parent_restaurant_id')
                    ->where('restaurant_id', $restaurant_id)
                    ->get();
				$res_addresses = Restaurant::where("parent_restaurant_id", $idParen[0]->parent_restaurant_id)->where("activate",1)->get();

                $shipping_charge=0;
                
                foreach ($res_addresses as $key => $value) {
					$resadds[$value->restaurant_id] = $value->name." - ".$value->address;
                        
                    if($value->restaurant_id==$value->parent_restaurant_id){
                        //Si es el padre usar ese costo de envío
                        $shipping_charge = $value->shipping_cost;
                    }
                                        
				}

				$sches = Schedule::where("restaurant_id", $restaurant_id)
					->where("day_id", date('w')+1)->get();

				//Aqui mando a traer las direcciones
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
                    $data['states'][$state->state_id]=$state->state;
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

	public function shipping_charge() {
        $input = Input::all();
        try {
            $statusCode = 200;

            $address = Address::findOrFail($input['address_id']);

            $response = array(
                "status" => true,
                "data" => $this->getShippingCharge($input['restaurant_id'], $address->zone_id)
            );
        } catch (Exception $e) {
            $statusCode = 400;
            $response = array(
                "status" => false,
                "data" => $e->getMessage()
            );
        }
        return Response::json($response, $statusCode);
    }

    public function getUserData(){
        #$id_user = Session::get('usuario_id');
        $input = Input::all();

        $usuario = DB::table('pf.com_users')
                        ->select('name','last_name','phone')
                        ->where('user_id', Auth::user()->user_id)
                        ->get();

        return Response::json($usuario);
    }

    private function getShippingCharge($restaurant_id, $zone_id) {
        return DB::table('restaurants_zones')->where('zone_id', $zone_id)
                ->where('restaurant_id', $restaurant_id)->first();
    }

    public function getTimeEst(){

        $idD = Input::get('direccion');

        $diaActual = date('w');
        $horaActual = date("H:i:s");
        #$horaActual = '23:00:00';
        $newDay = $diaActual + 1;

        $result = DB::table('pf.com_zones_traffic')
            ->join('diner_addresses', 'com_zones_traffic.zone_id', '=', 'diner_addresses.zone_id')
            ->where('com_zones_traffic.day_id',$newDay)
            ->where('com_zones_traffic.traffic_beginning','<=',$horaActual)
            ->where('com_zones_traffic.traffic_end','>=',$horaActual)
            ->where('diner_addresses.address_id',$idD)
            ->select('com_zones_traffic.prom_time')
            ->get();

        /*$result = DB::select('
            SELECT tra.prom_time FROM pf.com_zones_traffic as tra 
            inner join diner_addresses as da ON tra.zone_id = da.zone_id
            where day_id = "'.$diaActual.'" and 
            ("'.$horaActual.'" >= tra.traffic_beginning and "'.$horaActual.'" <= tra.traffic_end)
            and da.address_id = "'.$idD.'"
        ');*/
        return Response::json($result);
    }

    public function getOrderData(){
    	$type = Input::get('type');

    	if($type == 'email'){
    		$email = Input::get('mail');

    		$result = DB::SELECT('
		    			SELECT
		    				u.user_id as "user_id",
							u.email as "email",
							u.name  as "nombres",
							u.last_name as "apellidos",
							z.zone as "zona_envio",
							ifnull(u.phone,"") as "telefono_cliente",
							ifnull(o.customer_phone,"") as "telefono_cliente_orden",
							ifnull(o.customer,"") as "nombre_cliente_orden"
						FROM pf.diner_addresses d
						   	INNER JOIN com_users u
						   		ON d.user_id = u.user_id
						   	INNER JOIN com_zones z
							   	ON d.zone_id = z.zone_id
						   	left join req_orders o
							   	on d.address_id = o.address_id
						WHERE
							u.email = "'.$email.'"
						LIMIT 1
		    			');
    		
    		$cant = count($result);
    		Log::info($cant);

    		if ($cant >= 1) {
    			//Aqui mando a traer las direcciones
				$addrss = Address::where('user_id', $result[0]->user_id)->get();
				foreach ($addrss as $key => $value) {
					$usradds[$value->address_id] = $value->address_name ." - ". $value->address_1 .
						(!empty($value->address_2) ? ", ".$value->address_2 : "") .
						(!empty($value->city) ? ", ".$value->city : "");
				}

				if(!isset($usradds)){
					$usradds = array ();
				}
				
				$direcciones = $usradds;

	    		return Response::json(array('result'=>$result, 'direcciones'=>$direcciones));
    		}else{

    			$query = DB::SELECT('
    					SELECT
							u.user_id as "user_id",
							u.email as "email",
							u.name  as "nombres",
							u.last_name as "apellidos",
							u.phone as "telefono"
						FROM pf.com_users u		
						WHERE
							u.email = "'.$email.'"
						LIMIT 1
    				');

    			$conteo = count($query);

    			if($conteo > 0){
    				
    				$addr = Address::where('user_id', $query[0]->user_id)->get();
					$countAdrr = count($addr);

					if($countAdrr > 0){
						foreach ($addr as $key => $value) {
							$usradds[$value->address_id] = $value->address_name ." - ". $value->address_1 .
								(!empty($value->address_2) ? ", ".$value->address_2 : "") .
								(!empty($value->city) ? ", ".$value->city : "");
						}

						if(!isset($usradds)){
							$usradds = array ();
						}

						$direcciones = $usradds;
					}else{
						$direcciones = 0;
					}

	    			$result = 0;

	    			if(!isset($query)){
						$query = array ();
					}

	    			return Response::json(array('result'=>$result, 'direcciones'=>$direcciones, 'query' => $query));
    			}else{
    				$result = 0;
	    			$direcciones = 0;
	    			return Response::json(array('result'=>$result, 'direcciones'=>$direcciones));
    			}
    		}

    	}elseif($type == 'telefono'){
    		$number = Input::get('number');

    		$result = DB::SELECT('
			    			SELECT
			    				u.user_id as "user_id",
								u.email as "email",
								u.name  as "nombres",
								u.last_name as "apellidos",
								z.zone as "zona_envio",
								ifnull(u.phone,"") as "telefono_cliente",
								ifnull(o.customer_phone,"") as "telefono_cliente_orden",
								ifnull(o.customer,"") as "nombre_cliente_orden"
							FROM pf.diner_addresses d
							   	INNER JOIN com_users u
							   		ON d.user_id = u.user_id
							   	INNER JOIN com_zones z
								   	ON d.zone_id = z.zone_id
							   	left join req_orders o
								   	on d.address_id = o.address_id
							WHERE
								u.phone = "'.$number.'" OR
								o.customer_phone ="'.$number.'"
							LIMIT 1
		    			');

    		$cant = count($result);
    		Log::info($cant);

    		if ($cant >= 1) {
    			//Aqui mando a traer las direcciones
				$addrss = Address::where('user_id', $result[0]->user_id)->get();
				foreach ($addrss as $key => $value) {
					$usradds[$value->address_id] = $value->address_name ." - ". $value->address_1 .
						(!empty($value->address_2) ? ", ".$value->address_2 : "") .
						(!empty($value->city) ? ", ".$value->city : "");
				}

				if(!isset($usradds)){
					$usradds = array ();
				}
				
				$direcciones = $usradds;

	    		return Response::json(array('result'=>$result, 'direcciones'=>$direcciones));
    		}else{

    			$query = DB::SELECT('
    					SELECT
							u.user_id as "user_id",
							u.email as "email",
							u.name  as "nombres",
							u.last_name as "apellidos",
							u.phone as "telefono"
						FROM pf.com_users u		
						WHERE
							u.phone = "'.$number.'"
						LIMIT 1
    				');

    			$conteo = count($query);

    			if($conteo > 0){
    				
    				$addr = Address::where('user_id', $query[0]->user_id)->get();
					$countAdrr = count($addr);

					if($countAdrr > 0){
						foreach ($addr as $key => $value) {
							$usradds[$value->address_id] = $value->address_name ." - ". $value->address_1 .
								(!empty($value->address_2) ? ", ".$value->address_2 : "") .
								(!empty($value->city) ? ", ".$value->city : "");
						}

						if(!isset($usradds)){
							$usradds = array ();
						}

						$direcciones = $usradds;
					}else{
						$direcciones = 0;
					}

	    			$result = 0;

	    			if(!isset($query)){
						$query = array ();
					}

	    			return Response::json(array('result'=>$result, 'direcciones'=>$direcciones, 'query' => $query));
    			}else{
    				$result = 0;
	    			$direcciones = 0;
	    			return Response::json(array('result'=>$result, 'direcciones'=>$direcciones));
    			}
    		}    		
    	}
    }

     //Genera numero de orden de manera aleatoria
    public function numeroOrden($length = 0, $uc = FALSE, $n = TRUE, $sc = FALSE) {
        //se consulta la Hora y se le agrega al final para evitar que se repita el numero
        $hora = date("G");
        //se compara la cantidad de digitos para colocar el numero de digitos en length
        if (strlen($hora) == 2) {
            $length = 4;
        } else {
            $length = 5;
        }
        $source = 'ABCDEFGHJKMNPQRSTUVWXYZ';
        if ($uc == 1)
            $source .= 'abcdefghjkmnpqrstuvwxyz';
        if ($n == 1)
            $source .= '23456789';
        if ($sc == 1)
            $source .= '|@#~$%()=^*+[]{}-_';
        if ($length > 0) {
            $rstr = "";
            $source = str_split($source, 1);
            for ($i = 1; $i <= $length; $i++) {
                mt_srand((double) microtime() * 1000000);
                $num = mt_rand(1, count($source));
                $rstr .= $source[$num - 1];
            }
        }
        $rstr = $rstr . $hora;

        return $rstr;
    }


    public function store(){
        
        try{

            $rules = array(
                'service_type_id' => 'required|numeric',
                'payment_method_id' => 'required|numeric'
            );
            $display = array(
                'service_type_id' => 'Tipo de servicio',
                'payment_method_id' => 'M&eacute;todo de pago'
            );
            //Rules before defined
            $validator = Validator::make(Input::all(), $rules);
            //Field name translation
            $validator->setAttributeNames($display);

            if($validator->fails()){
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
                }elseif ($st_id == 2){
                    $irules = array_add($irules, 'restaurant_address', 'required|numeric');
                    $idisplay = array_add($idisplay, 'restaurant_address', 'Direcci&oacute;n de sucursal');
                }

                $pm_id = Input::get('payment_method_id');
                $valorccv = Input::get('nombre_tarjeta');
                $mes = Input::get('month');
                $año = Input::get('year');
                $mesActual = date("n");
                $añoActual = date("Y");
                #$test=1;
                
                if($pm_id == 1){
                    $irules = array_add($irules, 'cash', 'required');
                    $idisplay = array_add($idisplay, 'cash', 'Efectivo');
                }elseif ($pm_id == 2){
                    $irules = array_add($irules, 'user_credit', 'required|min:6');
                    $irules = array_add($irules, 'credit_card', 'required|luhn');
                    
                    if($mes < $mesActual && $año == $añoActual){
                        $irules = array_add($irules, 'month', 'required|date');
                        $irules = array_add($irules, 'year', 'required|date');
                    }else{
                        $irules = array_add($irules, 'month', 'required|numeric|min:1');
                    }
                    
                    $irules = array_add($irules, 'year', 'required|numeric|min:1');
                    
                    if($valorccv == 4){
                        $irules = array_add($irules, 'secure_code', 'required|min:4');
                    }else{
                        $irules = array_add($irules, 'secure_code', 'required|min:3');
                    }

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

                if($ivalidator->fails()){
                    $messages = $ivalidator->messages();
                    return Redirect::to('cart/checkout')
                        ->withErrors($ivalidator)
                        ->withInput(Input::except('credit_card', 'secure_code'));
                }else{
                    // return Response::json(array('message'=>'Guardar orden'), 200);
                    $idusuario = Auth::id();
                    if (Session::has('cart')){
                        $cart = Session::get('cart');
                    }else{
                        return Redirect::to('cart');
                    }

                    /* ############################################################## */
                    if(is_array($cart)){
                        foreach($cart as $req_restaurant => $req_order){

                            //------------- Se comienza la orden -------------------
                            if(!empty($idusuario) && $req_restaurant != 'name' && is_numeric($req_restaurant)){
                                //verificamos si corresponde al id de restaurante
                                //realizamos conteo de direcciones
                                $nuevaorden = new Order; //creamos una nueva orden
                                $service_type_id = Input::get('service_type_id');

                                /*CODIGO PARA INSERTAR NOMBRE Y TELEFONO DEL USUARIO A LA ORDEN*/
                                $nuevaorden->customer = Input::get('nombre_user');
                                $nuevaorden->customer_phone = Input::get('telefono_user');

                                $idRes = DB::Table('res_restaurants')
                                        ->select('parent_restaurant_id')
                                        ->where('restaurant_id', $req_restaurant)
                                        ->get();

                                $service_type = ServiceType::findOrFail($service_type_id);
                                $nuevaorden->service_type_id = $service_type->service_type_id;

                                if($service_type->service_type_id == 1 || $service_type->service_type_id == 3) {

                                    $nuevaorden->restaurant_id = $idRes[0]->parent_restaurant_id;
                                    $addresses = Address::where('user_id', $idusuario)->get();
                                    
                                    if(count($addresses) == 1){
                                        //si existe solo una dirección se trabajará con ella
                                        $direccion = $addresses[0];
                                    }else{
                                        // Si esxiste más de una dirección se espera parámetro para decidir con cual trabajará
                                        $direccion = (Input::has('address_id'))? Address::where('address_id', Input::get('address_id'))->first():$addresses[0];
                                    }

                                    $nuevaorden->address = $direccion->address_name . ' - ' . $direccion->address_1 .
                                        (!empty($direccion->address_2) ? ', ' . $direccion->address_2 : '');

                                    $nuevaorden->address_id=$direccion->address_id;

                                    //Definiendo costo de envío
                                    if($nuevaorden->service_type_id == 1){
                                        $restaurant = Restaurant::findOrFail($nuevaorden->restaurant_id);
                                        $nuevaorden->shipping_charge = $restaurant->shipping_cost;
                                    }else{
                                        $zone = DB::table('restaurants_zones')
                                            ->where('zone_id', $direccion->zone_id)->where('restaurant_id', $nuevaorden->restaurant_id)->first();
                                        $nuevaorden->shipping_charge = ($zone==NULL)? 0: $zone->shipping_charge;
                                    }
                                }elseif($service_type->service_type_id == 2){
                                    $restaurant_address = Input::get('restaurant_address');
                                    if(!empty($restaurant_address)) {
                                        $direccion = Restaurant::where('restaurant_id', $restaurant_address)->firstOrFail();
                                        $addr = $direccion->name . ' - ' . $direccion->address;
                                        $nuevaorden->restaurant_id = $restaurant_address;
                                        $nuevaorden->address = $addr;
                                        $nuevaorden->pickup_hour = Input::get('hour');
                                        $nuevaorden->pickup_min = Input::get('minutes');
                                    }else
                                        return 'Falta especificar la dirección'; //Falta mandar el parametro
                                }

                                /*Se recorren los productos para crear el order_total*/
                                if(is_array($req_order)){
                                    $totalF = 0;
                                    foreach ($req_order as $key => $req_product) {
                                        $product = Product::findOrFail($req_product['product_id']);
                                        $nuevo_total = ($product->value * $req_product['quantity']);
                                        $totalF += $nuevo_total;
                                    }
                                    $nuevaorden->order_total = $totalF;
                                }

                                $payment_method_id = Input::get('payment_method_id');
                                if(!empty($payment_method_id)) {//verificando el campo payment_method
                                    $payment_method = PaymentMethod::findOrFail($payment_method_id);
                                    $nuevaorden->payment_method_id = $payment_method->payment_method_id;
                                    
                                    $datosUsuario = User::where('user_id', $idusuario)->get();

                                    if($payment_method->payment_method_id == 2 && $datosUsuario[0]['sospecha'] != 1){//Si es pago con tarjeta
                                        $nuevaorden->credit_name = Input::get('user_credit');
                                        $nuevaorden->credit_card = Input::get('credit_card');
                                        $nuevaorden->credit_expmonth = Input::get('month');
                                        $nuevaorden->credit_expyear = Input::get('year');
                                        $nuevaorden->secure_code = Input::get('secure_code');

                                        $card = DB::table('res_payment_methods')->where('payment_method_id', 2)->first();
                                        $credit_charge = round((($totalF + $nuevaorden->shipping_charge) * $card->payment_method_charge),2);
                                        /*cargo de tarjeta*/
                                        $credit_charge= $credit_charge+0;
                                        $cad = explode(".", $credit_charge);

                                        log::info('cad: '.$cad[1]);
                                        $rest= substr($cad[1], 2, 1); //abcd
                                        
                                        log::info('rest TC: '.$rest);

                                        if($rest != null && $rest < 5) {
                                            $credit_charge=round(($credit_charge + 0.01),2);
                                            log::info('SI:credit_charge: '.$credit_charge);
                                        }else{
                                            $credit_charge=round(($credit_charge + 0.01),2);
                                            log::info('NO:credit_charge: '.$credit_charge);
                                        }

                                        $nuevaorden->credit_charge = $credit_charge;
                                        log::info('Sin aprox. cargo credit_charge: '.round((($totalF + $nuevaorden->shipping_charge) * $card->payment_method_charge),6));
                                        log::info('cargo credit_charge: '.$nuevaorden->credit_charge);

                                        /*Fin cargo tarjeta*/
                                        #$nuevaorden->credit_charge = $credit_charge;
                                        $transaccion = Input::get('transaction_id');
                                        //Se ingresa el id de la transacción
                                        if(isset($transaccion)){
                                            $nuevaorden->transaction_id = Input::get('transaction_id');    
                                        }

                                        $nuevaorden->order_cod = $this->numeroOrden();
                                        $nuevaorden->source_device = 'Web';
                                        $nuevaorden->save(); //Se hace update de precio final

                                        $order_stat = new OrderStatusLog;
                                        $order_stat->order_id = $nuevaorden->order_id;
                                        $order_stat->user_id = $idusuario;
                                        $order_stat->order_status_id = 2;
                                        $order_stat->save();

                                        // Asignar la orden al usuario
                                        $order_usr = new OrderRating;
                                        $order_usr->order_id = $nuevaorden->order_id;
                                        $order_usr->user_id = $idusuario;
                                        $order_usr->quality_rating = 0;
                                        $order_usr->speed_rating = 0;
                                        $order_usr->comment = '';
                                        $order_usr->rating_date = "0000-00-00 00:00:00";
                                        $order_usr->save();
                                        /*FIN CODIGO PAGO BAC*/
                                    }elseif($payment_method->payment_method_id == 2 && $datosUsuario[0]['sospecha']==1){
                                        return Redirect::to('cart/checkout')
                                            ->withErrors('Orden incobrable. Intente con otro método de pago.');
                                    }elseif($payment_method->payment_method_id == 1){
                                        $nuevaorden->pay_bill = Input::get('cash');
                                        $nuevaorden->order_cod = $this->numeroOrden();
                                        $nuevaorden->source_device = 'Web';
                                        $nuevaorden->save(); // Se guarda la orden padre

                                        $order_stat = new OrderStatusLog;
                                        $order_stat->order_id = $nuevaorden->order_id;
                                        $order_stat->user_id = $idusuario;
                                        $order_stat->order_status_id = 1;
                                        $order_stat->save();

                                        // Asignar la orden al usuario
                                        $order_usr = new OrderRating;
                                        $order_usr->order_id = $nuevaorden->order_id;
                                        $order_usr->user_id = $idusuario;
                                        $order_usr->quality_rating = 0;
                                        $order_usr->speed_rating = 0;
                                        $order_usr->comment = '';
                                        $order_usr->rating_date = "0000-00-00 00:00:00";
                                        $order_usr->save();
                                    }elseif($payment_method->payment_method_id == 3){//COMIENZA CODIGO PARA TIGO MONEY
                                        $nuevaorden->order_cod = $this->numeroOrden();
                                        $nuevaorden->num_tigo_money = Input::get('num_debitar');
                                        $nuevaorden->billetera_user = Input::get('billetera');
                                        #$nuevaorden->tigo_money_charge = Input::get('cargo_uso_tigo');

                                        $tigo = DB::table('res_payment_methods')->where('payment_method_id', 3)->first();
                                        $tigo_charge = round((($totalF + $nuevaorden->shipping_charge) * $tigo->payment_method_charge),2);
                                        
                                        /*cargo de tigo*/
                                        if($tigo_charge != 0){
                                            $tigo_charge= $tigo_charge+0;
                                            $cad = explode(".", $tigo_charge);

                                            //log::info('cad: '.$cad[1]);
                                            $rest= substr($cad[1], 2, 1); // abcd
                                            //log::info('rest TM: '.$rest);

                                            if($rest != null && $rest < 5) {
                                                $tigo_charge=round(($tigo_charge + 0.01),2);
                                                log::info('SI:tigo_charge: '.$tigo_charge);
                                            }else{
                                                $tigo_charge=round(($tigo_charge+0.01),2);
                                                log::info('NO:tigo_charge: '.$tigo_charge);
                                            }
                                        }else{
                                            $tigo_charge = 0;                                            
                                        }

                                        $nuevaorden->tigo_money_charge = $tigo_charge;
                                        log::info('Sin aprox. cargo tigo_charge: '.round((($totalF + $nuevaorden->shipping_charge) * $tigo->payment_method_charge),6));
                                        log::info('cargo tigo_charge: '.$nuevaorden->tigo_money_charge);

                                        /*FIN TIGO*/
                                        $nuevaorden->source_device = 'Web';
                                        $nuevaorden->save(); // Se guarda la orden padre

                                        $order_stat = new OrderStatusLog;
                                        $order_stat->order_id = $nuevaorden->order_id;
                                        $order_stat->user_id = $idusuario;
                                        $order_stat->order_status_id = 13;
                                        $order_stat->save();

                                        // Asignar la orden al usuario
                                        $order_usr = new OrderRating;
                                        $order_usr->order_id = $nuevaorden->order_id;
                                        $order_usr->user_id = $idusuario;
                                        $order_usr->quality_rating = 0;
                                        $order_usr->speed_rating = 0;
                                        $order_usr->comment = '';
                                        $order_usr->rating_date = "0000-00-00 00:00:00";
                                        $order_usr->save();
                                    }else
                                        return 'No existe tipo de pago';
                                }else
                                    return 'No existe tipo de pago';
                            }

                            if(is_array($req_order)){
                                foreach ($req_order as $key => $req_product){
                                    //Se comienza el detail de de la orden
                                    $detalle_producto = new OrderDetail;
                                    $detalle_producto->order_id = $nuevaorden->order_id;
                                    $detalle_producto->product_id = $req_product['product_id'];
                                    $detalle_producto->quantity = $req_product['quantity'];
                                    $detalle_producto->product = $req_product['product'];
                                    $detalle_producto->unit_price = $req_product['unit_price'];
                                    $detalle_producto->total_price = $req_product['total_price'];
                                    $detalle_producto->comment = $req_product['comment'];
                                    $detalle_producto->save();

                                    /*$product = Product::findOrFail($detalle_producto->product_id);
                                    $nuevo_total = ($product->value * $detalle_producto->quantity) + $nuevaorden->order_total;
                                    $nuevaorden->order_total = $nuevo_total;
                                    $nuevaorden->order_cod = $this->numeroOrden();*/
                                    
                                    #$nuevaorden->save(); //Se hace update de precio final
                                    
                                    if(!empty($req_product['ingredients'])){
                                        foreach ($req_product['ingredients'] as $ingrediente){
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
                                        //verificando las condiciones que hay en el producto
                                        foreach ($req_product['conditions'] as $condition) {
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
                                    $nuevaorden->pay_change = $nuevaorden->pay_bill - $nuevaorden->order_total - $nuevaorden->shipping_charge;
                                    $nuevaorden->save();
                                }
                            }
                        }
                    }

                    $response['status'] = true;
                    $response['data'] = $nuevaorden;
                    Session::forget('cart');
                    Session::forget('total_order');
                    Session::forget('cart2');

                    Session::put('flash_message1', 'Orden creada correctamente.');
                    return Redirect::to('/delivery_pidafacil');
                    /* ############################################################## */
                }
            }
        }catch(\Illuminate\Database\QueryException $e){
            $error = $e->getMessage();
            $cadena = explode('@', $e);

            return Redirect::to('cart/checkout')
                ->withErrors($cadena[1]);
        }
    }

    //Función para eliminar todo el carrito
    public function destroy(){
		try {
			if (Session::has('cart')){
				Session::forget('cart');
				Session::forget('cart2');
				//$response = array("message_success" => "Todos los productos eliminados.");
				return Redirect::action('OrderController@allRest');
			}
		} catch (Exception $e) {
			return Response::json(array('message_error' => $e->getMessage()), 400);
		}
	}

	//Función para eliminar un producto específico del carrito
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
					
					return Redirect::action('OrderController@allRest');
				}
			}
		} catch (Exception $e) {
			return Response::json(array('message_error' => $e->getMessage()), 400);
		}
	}

}