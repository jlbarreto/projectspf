<?php

class RestaurantOrdersController extends \BaseController {



	/**
	 * Display the specified resource.
	 * GET /restaurantorders/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		/*
		$limit = User::find(1)->restaurants->count();
		if($limit > 0){
			$resu = User::find(1)->restaurants;
			return Response::json($resu, 200);
		}else{
			return $limit;
		}
		*/
		$restaurant =  Restaurant::findOrFail($id);
		$limit = Order::where('restaurant_id', $restaurant->restaurant_id)
			->whereNotNull('address')->count();
		
		if($limit > 0) {
			$pending 	= 0;
			$registered = 0;
			$accepted 	= 0;
			$dispatched = 0;
			$delivered	= 0;
			$cancelled 	= 0;
			$rejected 	= 0;
			$uncollectible = 0;

			$results = DB::select('
			SELECT MAX(req_order_status_logs.order_status_id) as ultimo, req_orders.order_id
			FROM req_order_status_logs
			LEFT JOIN req_orders ON req_order_status_logs.order_id = req_orders.order_id
			WHERE req_orders.restaurant_id = ? AND NOT ISNULL(req_orders.address)
			GROUP BY req_orders.order_id', array($restaurant->restaurant_id));
			
			foreach ($results as $value) {
				switch($value->ultimo){
					case 1: $pending  	+= 1; break;
					case 2: $registered += 1; break;
					case 3: $accepted  	+= 1; break;
					case 4: $dispatched += 1; break;
					case 5: $delivered 	+= 1; break;
					case 6: $cancelled 	+= 1; break;
					case 7: $rejected 	+= 1; break;
					case 8: $uncollectible += 1; break;
					default: break;
				}
			}

			$delivery = 0;
			$pickup   = 0;

			$orders = Order::where('restaurant_id', $restaurant->restaurant_id)
				->whereNotNull('address')
				->with('statusLogs')->with('serviceType')->with('paymentMethod')
				->with('products')->with('users')
				->orderBy('req_orders.created_at', 'asc')->get();
			foreach ($orders as $k => $val) {
				switch ($val->service_type_id) {
					case 1: $delivery += 1; break;
					case 2: $pickup   += 1; break;
					default: break;
				}
			}

			$stats = array(
				'pending' 	=> $pending,
				'registered'=> $registered,
				'accepted' 	=> $accepted,
				'dispatched'=> $dispatched,
				'delivered' => $delivered,
				'cancelled' => $cancelled,
				'rejected' 	=> $rejected,
				'uncollectible' => $uncollectible,
				'delivery' 	=> $delivery,
				'pickup' 	=> $pickup
			);
		}else{
			$orders = null;	
			$stats = null;
		}
		/*
		$results = DB::select('select orders.restaurant_id,user.user_id, user.email, user.name, user.last_name,orders.order_id, orders.order_total, orders.address_id, address.address_1,
			address.address_2, address.address_name, address.city, address.country_id, address.state, 
			orders.order_status_id, order_status.order_status, orders.service_type_id, service.service_type,
			orders.payment_method_id, payment.payment_method, orders.created_at, orders.updated_at from pidafacil.req_orders as orders 
			inner join pidafacil.diner_addresses as address on orders.address_id = address.address_id
			inner join pidafacil.req_order_status as order_status on orders.order_status_id = order_status.order_status_id
			inner join pidafacil.res_service_types as service on orders.service_type_id = service.service_type_id
			inner join pidafacil.res_payment_methods as payment on orders.payment_method_id =  payment.payment_method_id
			inner join pidafacil.com_users as user on address.user_id = user.user_id
			where orders.restaurant_id = ? and order_status.order_status_id = 1 order by orders.created_at desc;', array($id));
		*/

		return View::make('web.visor')
			->with('orders', $orders)
			->with('stats', $stats);
	}


	/**
	 * Show the specific order.
	 * GET /restaurantorders/order/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function order($id)
	{
		$n=0;
		$req_products = DB::select('select orders.order_id, details.order_det_id,orders.restaurant_id, product.product_id, product.product
		from req_orders as orders 
		inner join req_orders_det as details on orders.order_id = details.order_id
		inner join res_products as product on details.product_id = product.product_id
		where orders.order_id = ?', array( $id));

		foreach ($req_products as $product) {

			$data['producto['.$n.']']=$product;
			
			$ingredient = DB::select('select resingredient.ingredient_id, resingredient.ingredient,reqingredient.remove from req_product_ingredients reqingredient 
			inner join res_ingredients resingredient on reqingredient.ingredient_id =  resingredient.ingredient_id
			where reqingredient.order_det_id = ? and reqingredient.remove != 0',array($product->order_det_id));
			$data['ingredients['.$n.']'] = $ingredient;


			$optioncondition = DB::select('select  orderdetail.order_det_id,conditions.condition,conditionoption.condition_option from req_orders_det orderdetail
			inner join req_product_conditions_options prodcondition on orderdetail.order_det_id = prodcondition.order_det_id
			inner join res_conditions conditions on conditions.condition_id  =  prodcondition.condition_id
			inner join res_product_conditions_options conditionoption on conditionoption.condition_option_id = prodcondition.condition_option_id 
			where orderdetail.order_det_id = ?',array($product->order_det_id));
			$data['optioncondition['.$n.']'] = $optioncondition;


			$n++;
			
		}
		$data['info'] = DB::select('select orders.restaurant_id,user.user_id, user.email, user.name, user.last_name,orders.order_id, orders.order_total, orders.address_id, address.address_1,
			address.address_2, address.address_name, address.city, address.country_id, address.state, 
			orders.order_status_id, order_status.order_status, orders.service_type_id, service.service_type,
			orders.payment_method_id, payment.payment_method, orders.created_at, orders.updated_at from req_orders as orders 
			inner join diner_addresses as address on orders.address_id = address.address_id
			inner join req_order_status as order_status on orders.order_status_id = order_status.order_status_id
			inner join res_service_types as service on orders.service_type_id = service.service_type_id
			inner join res_payment_methods as payment on orders.payment_method_id =  payment.payment_method_id
			inner join com_users as user on address.user_id = user.user_id
			where  orders.order_id = ? ', array($id));
		$data['nprods'] = $n;
		return $data;		
		//return $results;
	}
	/**
	 * Show the form for editing the specified resource.
	 * POST /restaurant-orders/forward/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function forward($id)
	{
		$order = Order::findOrFail($id);
		$result = DB::select('
			SELECT MAX(req_order_status_logs.order_status_id) as ultimo
			FROM req_order_status_logs WHERE order_id = ?', array($order->order_id));
		switch ($result[0]->ultimo) {
			case 1:
				$order_stat = new OrderStatusLog;
				$order_stat->order_id = $order->order_id;
				$order_stat->user_id = Auth::id();
				$order_stat->order_status_id = 3;
				$order_stat->save();
				break;
			case 2:
				$order_stat = new OrderStatusLog;
				$order_stat->order_id = $order->order_id;
				$order_stat->user_id = Auth::id();
				$order_stat->order_status_id = 3;
				$order_stat->save();
				break;
			case 3:
				$order_stat = new OrderStatusLog;
				$order_stat->order_id = $order->order_id;
				$order_stat->user_id = Auth::id();
				$order_stat->order_status_id = 4;
				$order_stat->save();
				break;
			case 4:
				$order_stat = new OrderStatusLog;
				$order_stat->order_id = $order->order_id;
				$order_stat->user_id = Auth::id();
				$order_stat->order_status_id = 5;
				$order_stat->save();
				break;
			default: $order_stat=null; break;				
		}
		return Redirect::to('/restaurant-orders/'.$order->restaurant_id);		
	}
	/**
	 * Show the form for editing the specified resource.
	 * POST /restaurantorders/forward/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function backward($id)
	{
		$order = Order::findOrFail($id);
		switch ($order->order_status_id) {
			case 1:
				$order->order_status_id = 1;
				break;
			case 2:
				$order->order_status_id = 1;
				break;
			case 3:
				$order->order_status_id = 2;
				break;
			case 4:
				$order->order_status_id = 3;
				break;
			case 5:
				$order->order_status_id = 4;
				break;

			default:
				'';
				break;				
		}
		$order->save();
	}

	/**
	 * Update the specified resource in storage.
	 * POST /restaurantorders/cancel/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function cancel($id)
	{
		// Cancelar la orden
		$order = Order::findOrFail($id);
		$result = DB::select('
			SELECT MAX(req_order_status_logs.order_status_id) as ultimo
			FROM req_order_status_logs WHERE order_id = ?', array($order->order_id));
		if($result[0]->ultimo == 1 || $result[0]->ultimo == 2){
			$cancel = Input::get('rejected');
			if($cancel >= 6 && $cancel <= 8){		
				$order_stat = new OrderStatusLog;
				$order_stat->order_id = $order->order_id;
				$order_stat->user_id = Auth::id();
				$order_stat->order_status_id = Input::get('rejected');
				$order_stat->comment = Input::get('comment');
				$order_stat->save();
			}
		}
		
		return Redirect::to('/restaurant-orders/'.$order->restaurant_id);
	}

	/**
	 * Remove the specified resource from storage.
	 * POST /restaurantorders/baddebt/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function baddebt($id)
	{
		$order = Order::findOrFail($id);
		$order->order_status_id=8;
		$order->save();
	}


	/**
	 * Remove the specified resource from storage.
	 * POST /restaurantorders/baddebt/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function reject($id)
	{
		$order = Order::findOrFail($id);
		$order->order_status_id=7;
		$order->save();
	}

	/**
	 * Remove the specified resource from storage.
	 * POST /restaurantorders/baddebt/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function accept($id)
	{
		$order = Order::findOrFail($id);
		$order->order_status_id=3;
		$order->save();
	}

	

	

}