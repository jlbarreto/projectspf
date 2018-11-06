<?php

class HomeController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function showWelcome(){
		return View::make('hello');
	}


	public function coordenadasMoto(){
		$date = new DateTime();
    	$fecha = $date->format('Y-m-d');

    	if(Request::ajax()){
	    	$result = 	DB::select('
				    		select msl.mensajero_coordenadas, msl.motorista_id, ms.mensajero_status as estado, msl.mensajero_status_id as ultimo
							from mensajero_status_log as msl
							INNER JOIN pf.mensajero_status as ms ON msl.mensajero_status_id = ms.mensajero_status_id
							where msl.mensajero_status_id = 15
							and msl.created_at LIKE "%'.$fecha.'%"
							group by msl.motorista_id
							order by msl.mensajero_status_id DESC;
						');

	    	$result2 = DB::select('
								SELECT msl.mensajero_coordenadas, ms.mensajero_status as estado, msl.motorista_id, ord.order_cod, ord.order_id, msl.mensajero_log_id as estadoFin, max(msl.mensajero_status_id) as ultimo,
									if(os.order_status_id = 5,"Entregada",os.order_status) AS "condicion"
								FROM pf.mensajero_status_log as msl
								INNER JOIN req_orders as ord ON msl.order_id = ord.order_id
								inner join req_order_status_logs l
									ON l.order_id = ord.order_id
								inner join req_order_status os
									ON os.order_status_id = l.order_status_id
								inner join (SELECT order_id,
										Max(order_status_id) AS order_status_id
									FROM   pf.req_order_status_logs
									GROUP  BY order_id) AS lot
								ON lot.order_id = ord.order_id
							  	AND lot.order_status_id = os.order_status_id
								INNER JOIN pf.mensajero_status as ms ON msl.mensajero_status_id = ms.mensajero_status_id
								WHERE msl.created_at LIKE "%'.$fecha.'%"
								and msl.mensajero_status_id < 15
								GROUP BY msl.motorista_id, msl.order_id
								order by msl.order_id DESC;
	    					');

	    	$result3 = DB::select('
							select ord.order_id, ord.order_cod, ord.customer, ord.customer_phone, ord.address_id, res.map_coordinates, res.name, res.slug, res.restaurant_id, da.map_coordinates as destino, da.address_name, osi.order_status_id as ultimo
							from req_orders as ord
							INNER JOIN res_restaurants as res ON ord.restaurant_id = res.restaurant_id
							INNER JOIN diner_addresses as da ON ord.address_id = da.address_id
							INNER JOIN req_order_status_logs as osi ON ord.order_id = osi.order_id
							where ord.created_at LIKE "%'.$fecha.'%"
							and res.map_coordinates <> 0
							group by ord.order_id
    					');

	    	$sum1 = count($result) -1;
	    	$sum2 = count($result2) -1;
	    	$i=0;
	    	$max = array();

	    	Log::info('result = '.$sum1. ' Y result2 = '.$sum2);
	    	
	    	$array = json_decode(json_encode($result2), true);

	    	foreach ($result as $key => $value) {

	    		foreach ($result2 as $key => $value2){
	    			if($value2->motorista_id == $value->motorista_id){
						//$newA[] = array('motorista_id'=>$value2->motorista_id, 'mensajero_coordenadas'=>$value->mensajero_coordenadas, 'estado'=> $value2->mensajero_status, 'ultimo' => $value2->ultimo);
						$value2->mensajero_coordenadas = $value->mensajero_coordenadas;
	    			}
	    		}
	    	}

	    	foreach ($result3 as $key3 => $value3) {
	    		$max[] = DB::select('
					      SELECT osl.order_id, MAX(osl.order_status_id) as ultimo2
					      FROM req_order_status_logs as osl WHERE osl.order_id = "'.$value3->order_id.'"
				      	');
	    	}

	    	foreach ($result2 as $key3 => $value3) {
	    		$max2[] = DB::select('
					      SELECT osl.order_id, MAX(osl.order_status_id) as ultimo2
					      FROM req_order_status_logs as osl WHERE osl.order_id = "'.$value3->order_id.'"
				      	');
	    	}
			
	    	foreach ($result3 as $key4 => $value4) {
	    		foreach ($max as $value5) {
	    			foreach ($value5 as $value6) {
	    				if($value6->order_id == $value4->order_id){
	    					#Log::info('ANTES '.$value4->ultimo);
	    					$value4->ultimo = $value6->ultimo2;
	    					#Log::info('DESPUES '.$value4->ultimo);
	    				}
	    			}
	    		}
	    	}

	    	foreach ($result2 as $key4 => $value4) {
	    		foreach ($max2 as $value5) {
	    			foreach ($value5 as $value6) {
	    				if($value6->order_id == $value4->order_id){
	    					Log::info('ANTES2 '.$value4->ultimo);
	    					$value4->estadoFin = $value6->ultimo2;
	    					Log::info('DESPUES2 '.$value4->estadoFin);
	    				}
	    			}
	    		}
	    	}

	    	Log::info($result);
	    	Log::info($result2);
	    	Log::info($result3);
			return Response::json(array($result, $result2, $result3));
	    }
	}
}
