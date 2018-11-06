<?php

class CarrierRestaurantController extends \BaseController {

  	public function __construct(EmailController $emails){
    	$this->email = $emails;
  	}

  	public function stats($orders, $res_id){
	    $pending = array('fillter'=>0,'delivery'=>0,'pickup'=>0,'pidafacil'=>0);
	    $registered = array('fillter'=>0,'delivery'=>0,'pickup'=>0,'pidafacil'=>0);
	    $accepted = array('fillter'=>0,'delivery'=>0,'pickup'=>0,'pidafacil'=>0);
	    $dispatched = array('fillter'=>0,'delivery'=>0,'pickup'=>0,'pidafacil'=>0);
	    $delivered = array('fillter'=>0,'delivery'=>0,'pickup'=>0,'pidafacil'=>0);
	    $cancelled = array('fillter'=>0,'delivery'=>0,'pickup'=>0,'pidafacil'=>0);
	    $rejected = array('fillter'=>0,'delivery'=>0,'pickup'=>0,'pidafacil'=>0);
	    $uncollectible = array('fillter'=>0,'delivery'=>0,'pickup'=>0,'pidafacil'=>0);
	    $unassigned = array('fillter'=>0,'delivery'=>0,'pickup'=>0,'pidafacil'=>0);

	    if($res_id == 0){
	      	$results = DB::select('
	      	SELECT MAX(req_order_status_logs.order_status_id) as ultimo, req_orders.order_id,req_orders.viewed_pidafacil,req_orders.service_type_id as type_id
	      	FROM req_order_status_logs
	      	LEFT JOIN req_orders ON req_order_status_logs.order_id = req_orders.order_id
	      	WHERE  req_orders.service_type_id = 3 AND NOT ISNULL(req_orders.address)
	      	GROUP BY req_orders.order_id');

	      	foreach ($results as $value){
	        	if (($value->ultimo == 1)){
	          		$pending['fillter'] += 1;
	        	}

		        switch($value->ultimo){
		          /*case 1: $pending['fillter']  += 1; break;
		          case 2: $pending['fillter'] += 1; break;
		          case 2: $registered['fillter'] += 1; break;*/
		          case 3: $accepted['fillter']    += 1; break;
		          case 4: $dispatched['fillter'] += 1;  break;
		          case 5: $delivered['fillter']   += 1; break;
		          case 6: $cancelled['fillter']   += 1; break;
		          case 7: $rejected['fillter']  += 1; break;
		          case 8: $uncollectible['fillter'] += 1; break;
		          case 12: $unassigned['fillter'] += 1; break;
		          default: break;
		        }
	      	}
	    }

	    $delivery = 0;
	    $pickup   = 0;
	    $pidafacil   = 0;

	    //se suman los tipos de servicio de acuerdo al estado del pedido para poder filtrar
	    foreach ($orders as $k => $val){
	      	$staId=0;
	      	foreach($val->statusLogs as $d){
	        	$staId=$d->order_status_id;
	      	}
	      	if($staId == 1 || $staId == 2){
	        	if($res_id == 0){
	          		if($staId == 1){
	            		switch ($val->service_type_id){
	              			case 1:
	              				$pending['delivery'] += 1;
	              			break;
	              			case 2:
	              				$pending['pickup'] += 1;
	              			break;
	              			case 3:
	              				$pending['pidafacil'] += 1;
	              			break;
		              		default:
		              		break;
	            		}
		          	}else{
		            	switch($val->service_type_id){
		              		case 1:
		              			$registered['delivery'] += 1;
		          			break;
		              		case 2:
		              			$registered['pickup'] += 1;
		              		break;
		              		case 3:
		              			$registered['pidafacil'] += 1;
		              		break;
			          		default:
			              	break;
		            	}
	          		}
	        	}else{
	          		switch($val->service_type_id){
	            		case 1:
	            			$pending['delivery'] += 1;
	            		break;
	            		case 2:
	            			$pending['pickup'] += 1;
	            		break;
	            		case 3:
	            			$pending['pidafacil'] += 1;
	            		break;
	            		default:
	            		break;
	          		}
	        	}
	      	}elseif($staId == 3){
	        	switch ($val->service_type_id){
	          		case 1: $accepted['delivery'] += 1; break;
	      			case 2: $accepted['pickup']   += 1; break;
	          		case 3: $accepted['pidafacil']   += 1; break;
	          		default: break;
	        	}
	      	}elseif($staId == 4){
	        	switch($val->service_type_id){
	          		case 1: $dispatched['delivery'] += 1; break;
	          		case 2: $dispatched['pickup']   += 1; break;
	          		case 3: $dispatched['pidafacil']   += 1; break;
	          		default: break;
	        	}
	      	}elseif($staId == 5){
	        	switch($val->service_type_id){
	          		case 1: $delivered['delivery'] += 1; break;
	          		case 2: $delivered['pickup']   += 1; break;
	          		case 3: $delivered['pidafacil']   += 1; break;
	          		default: break;
	        	}	
	      	}elseif($staId == 6){
	        	switch($val->service_type_id){
	          		case 1: $cancelled['delivery'] += 1; break;
	          		case 2: $cancelled['pickup']   += 1; break;
	          		case 3: $cancelled['pidafacil']   += 1; break;
	          		default: break;
	        	}
	      	}elseif($staId == 7){
	        	switch($val->service_type_id){
	          		case 1: $rejected['delivery'] += 1; break;
	          		case 2: $rejected['pickup']   += 1; break;
	          		case 3: $rejected['pidafacil']   += 1; break;
	          		default: break;
	        	}
	      	}elseif($staId == 8){
	        	switch ($val->service_type_id) {
	          		case 1: $uncollectible['delivery']  += 1; break;
	          		case 2: $uncollectible['pickup']    += 1; break;
	          		case 3: $uncollectible['pidafacil']       += 1; break;
	          		default: break;
	        	}
	      	}elseif($staId == 12){
	        	switch ($val->service_type_id) {
	          		case 1: $unassigned['delivery']  += 1; break;
	          		case 2: $unassigned['pickup']    += 1; break;
	          		case 3: $unassigned['pidafacil']       += 1; break;
	          		default: break;
	        	}
	      	}
	      	switch($val->service_type_id){
	        	case 1: $delivery  += 1; break;
	        	case 2: $pickup    += 1; break;
	        	case 3: $pidafacil    += 1; break;
	        	default: break;
	      	}
	    }
	    $stats = array(
	      	'pending'   => $pending,
	      	'registered'=> $registered,
	      	'accepted'  => $accepted,
	      	'dispatched'=> $dispatched,
	      	'delivered' => $delivered,
	      	'cancelled' => $cancelled,
	      	'rejected'  => $rejected,
	      	'uncollectible' => $uncollectible,
	      	'unassigned' => $unassigned,
	      	'delivery'  => $delivery,
	      	'pickup'  => $pickup,
	      	'pidafacil' => $pidafacil,
	    );

	    $stats['pending']['fillter'] = $stats['pending']['delivery'] + $stats['pending']['pickup'] + $stats['pending']['pidafacil'];
	    $stats['registered']['fillter'] = $stats['registered']['delivery'] + $stats['registered']['pickup'] + $stats['registered']['pidafacil'];
	    $stats['accepted']['fillter'] = $stats['accepted']['delivery'] + $stats['accepted']['pickup'] + $stats['accepted']['pidafacil'];
	    $stats['dispatched']['fillter'] = $stats['dispatched']['delivery'] + $stats['dispatched']['pickup'] + $stats['dispatched']['pidafacil'];
	    $stats['delivered']['fillter'] = $stats['delivered']['delivery'] + $stats['delivered']['pickup'] + $stats['delivered']['pidafacil'];
	    $stats['cancelled']['fillter'] = $stats['cancelled']['delivery'] + $stats['cancelled']['pickup'] + $stats['cancelled']['pidafacil'];
	    $stats['rejected']['fillter'] = $stats['rejected']['delivery'] + $stats['rejected']['pickup'] + $stats['rejected']['pidafacil'];
	    $stats['uncollectible']['fillter'] = $stats['uncollectible']['delivery'] + $stats['uncollectible']['pickup'] + $stats['uncollectible']['pidafacil'];
	    $stats['unassigned']['fillter'] = $stats['unassigned']['delivery'] + $stats['unassigned']['pickup'] + $stats['unassigned']['pidafacil'];
	    
	    return $stats;
  	}

  	public function view_pedidos_rest(){
  		$date = new DateTime();
    	$fecha = $date->format('Y-m-d');
    	$condi="";
    	$user_id = Auth::id();
    	$motoristas = DB::select('select * from motoristas LIMIT 10');

    	$restaurant_id = DB::table('res_user')
            ->select('restaurant_id')
            ->where('user_id',$user_id)
            ->get();
            
        $id_r = $restaurant_id[0]->restaurant_id;
    	$res = Restaurant::get();
    	$orders = Order::where('service_type_id', 3)
            ->where('created_at','LIKE','%'.$fecha.'%')
            ->where('viewed_pidafacil',1)
            ->where('restaurant_id', $id_r)
            ->get();

    	$condi = DB::table("res_conditions")->get();
    	$stats = $this->stats($orders,0);

  		return View::make('web.c_rest.pedidos_rest')
  		->with('orders', $orders)
    	->with('condi', $condi)
    	->with('res', $res)
    	->with('motoristas', $motoristas);
  	}

  	public function view_reporteV_rest(){  		
    	return View::make('web.c_rest.reporte_ventas_rest');
	}

  	public function view_reporteD_rest(){
  		$user_id = Auth::id();
		$restaurant_id = DB::table('res_user')
            ->select('restaurant_id')
            ->where('user_id',$user_id)
            ->get();
        
        $id_r = $restaurant_id[0]->restaurant_id;

  		$motoristas = DB::select('select * from motoristas LIMIT 10');
	    $res = Restaurant::get();
	    $orders = DB::table('req_orders')
	    		->join('res_restaurants', 'req_orders.restaurant_id', '=', 'res_restaurants.restaurant_id')
	    		->where('req_orders.service_type_id', 3)
	            ->where('req_orders.viewed_pidafacil',1)
	            ->where('req_orders.restaurant_id', $id_r)
	            ->get();

	    $condi = DB::table("res_conditions")->get();

	    return View::make('web.c_rest.reporte_detalle_rest')
	    ->with('motoristas', $motoristas);
  	}

  	public function view_tiempos_rest(){
  		return View::make('web.c_rest.reporte_tiempos_rest');
  	}

  	public function generarReporte_rest(){
  		$user_id = Auth::id();
  		$restaurant_id = DB::table('res_user')
            ->select('restaurant_id')
            ->where('user_id',$user_id)
            ->get();
            
        $id_r = $restaurant_id[0]->restaurant_id;
  		if(Request::ajax()){
	      	$motivo = Input::get('motivo');

	      	if($motivo == 'fecha'){
		        $fecha1 = Input::get('fecha_inicio');
	        	$fecha2 = Input::get('fecha_fin');

	        	Log::info('Fechas: '.$fecha1. ' '.$fecha2);

	        	$ordenes = DB::select('
	          		select ord.order_id, ord.order_cod, ord.customer, ord.shipping_charge, ord.pay_change, ord.created_at, da.reference, ord.order_total, ord.payment_method_id, ord.address, res.name, res.commission_percentage, mot.nombre, cz.zone, cz.cobro_motoboy, if(max(os.order_status_id) > 9,"Pendiente",max(os.order_status_id)) AS order_status_id, max(l.created_at) as tiempoC
	          		from req_orders as ord
	          		inner join res_restaurants as res ON ord.restaurant_id = res.restaurant_id
	          		inner join mensajero as rm ON ord.order_id = rm.order_id
          			inner join motoristas as mot ON rm.motorista_id = mot.motorista_id
	          		inner join diner_addresses as da ON ord.address_id = da.address_id
	          		inner join com_zones as cz ON da.zone_id = cz.zone_id
	          		inner join req_order_status_logs l 
	            		ON l.order_id = ord.order_id 
          			inner join req_order_status os 
	            		ON os.order_status_id = l.order_status_id 
	          		inner join (SELECT order_id, 
	            	Max(order_status_id) AS order_status_id 
	              	FROM pf.req_order_status_logs 
	              	GROUP BY order_id) AS lot 
	              		ON lot.order_id = ord.order_id 
	              	AND lot.order_status_id = os.order_status_id 
	          		where (ord.created_at BETWEEN "'.$fecha1.'" AND "'.$fecha2.'")
	          		and ord.service_type_id = 3
	          		and ord.restaurant_id = "'.$id_r.'"
	          		group by ord.order_id
	        	');
	      	}elseif($motivo == 'moto'){
	        	$moto = Input::get('motorista');
	        	$date = new DateTime();
	        	$fecha = $date->format('Y-m-d');

	        	if($moto == 'todos'){
	          		$ordenes = DB::select('
	            	select ord.order_id, ord.order_cod, ord.customer, ord.shipping_charge, ord.pay_change, ord.created_at, da.reference, ord.order_total, ord.payment_method_id, ord.address, res.name, res.commission_percentage, mot.nombre, cz.zone, cz.cobro_motoboy, if(max(os.order_status_id) > 9,"Pendiente",max(os.order_status_id)) AS order_status_id, max(l.created_at) as tiempoC
	            	from req_orders as ord
	            	inner join res_restaurants as res ON ord.restaurant_id = res.restaurant_id
	            	inner join mensajero as rm ON ord.order_id = rm.order_id
	            	inner join motoristas as mot ON rm.motorista_id = mot.motorista_id
	            	inner join diner_addresses as da ON ord.address_id = da.address_id
	            	inner join com_zones as cz ON da.zone_id = cz.zone_id
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
	            	where ord.created_at LIKE "%'.$fecha.'%" and ord.service_type_id = 3
	            	and ord.restaurant_id = "'.$id_r.'"
	            	group by ord.order_id
	          	');          
		        }else{
		          	$ordenes = DB::select('
		            	select ord.order_id, ord.order_cod, ord.customer, ord.shipping_charge, ord.pay_change, da.reference, res.commission_percentage, ord.order_total, ord.created_at, ord.payment_method_id, ord.address,res.name,mot.nombre, cz.zone, cz.cobro_motoboy, if(max(os.order_status_id) > 9,"Pendiente",max(os.order_status_id)) AS order_status_id, max(l.created_at) as tiempoC
		            	from req_orders as ord
		            	inner join res_restaurants as res ON ord.restaurant_id = res.restaurant_id
		            	inner join mensajero as rm ON ord.order_id = rm.order_id
		            	inner join motoristas as mot ON rm.motorista_id = mot.motorista_id
		            	inner join diner_addresses as da ON ord.address_id = da.address_id
		            	inner join com_zones as cz ON da.zone_id = cz.zone_id
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
		            	where rm.motorista_id = '.$moto.' and ord.created_at LIKE "%'.$fecha.'%" and ord.service_type_id = 3
		            	and ord.restaurant_id = "'.$id_r.'"
		            	group by ord.order_id
		          	');
		        }
	      	}elseif($motivo == 'pago'){
		        $tipo_pago = Input::get('pago');
		        $date = new DateTime();
		        $fecha = $date->format('Y-m-d');

		        if($tipo_pago == 'todos'){
		          $ordenes = DB::select('
		            select ord.order_id, ord.order_cod, ord.customer, ord.shipping_charge, ord.pay_change, da.reference, res.commission_percentage, ord.order_total, ord.created_at, ord.payment_method_id, ord.address,res.name,mot.nombre, cz.zone, cz.cobro_motoboy, if(max(os.order_status_id) > 9,"Pendiente",max(os.order_status_id)) AS order_status_id, max(l.created_at) as tiempoC
		            from req_orders as ord
		            inner join res_restaurants as res ON ord.restaurant_id = res.restaurant_id
		            inner join mensajero as rm ON ord.order_id = rm.order_id
		            inner join motoristas as mot ON rm.motorista_id = mot.motorista_id
		            inner join diner_addresses as da ON ord.address_id = da.address_id
		            inner join com_zones as cz ON da.zone_id = cz.zone_id
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
		            where ord.created_at LIKE "%'.$fecha.'%" and ord.service_type_id = 3
		            and ord.restaurant_id = "'.$id_r.'"
		            group by ord.order_id
		          ');
		        }else{
		          $ordenes = DB::select('
		            select ord.order_id, ord.order_cod, ord.customer, ord.shipping_charge, ord.pay_change, da.reference, res.commission_percentage, ord.order_total, ord.created_at, ord.payment_method_id, ord.address,res.name,mot.nombre, cz.zone, cz.cobro_motoboy, if(max(os.order_status_id) > 9,"Pendiente",max(os.order_status_id)) AS order_status_id, max(l.created_at) as tiempoC
		            from req_orders as ord
		            inner join res_restaurants as res ON ord.restaurant_id = res.restaurant_id
		            inner join mensajero as rm ON ord.order_id = rm.order_id
		            inner join motoristas as mot ON rm.motorista_id = mot.motorista_id
		            inner join diner_addresses as da ON ord.address_id = da.address_id
		            inner join com_zones as cz ON da.zone_id = cz.zone_id
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
		            where ord.payment_method_id = '.$tipo_pago.' and ord.created_at LIKE "%'.$fecha.'%" and ord.service_type_id = 3
		            and ord.restaurant_id = "'.$id_r.'"
		            group by ord.order_id
		          ');
		        }
	      	}
	      	#echo "<pre>"; print_r($ordenes); echo "</pre>"; die();
	      	Log::info($ordenes);
	      	return Response::json($ordenes);
	    }
  	}

  	public function generar_reporteV_rest(){
  		$user_id = Auth::id();
  		$restaurant_id = DB::table('res_user')
            ->select('restaurant_id')
            ->where('user_id',$user_id)
            ->get();
            
        $id_r = $restaurant_id[0]->restaurant_id;
        Log::info('EL ID ES: '.$id_r);
  		if(Request::ajax()){

	  		$motivo = Input::get('motivo');

	      	if($motivo == 'fecha'){
		        $fecha1 = Input::get('fecha_inicio');
		        $fecha2 = Input::get('fecha_fin');

		        $ordenes = DB::select("
		        	SELECT Date_format(o.created_at, '%d/%m/%y')
						AS 'fecha',
					SUM(TRUNCATE(( o.order_total ) - ( ( o.order_total ) * Round(( re.commission_percentage / 100 ), 2) ), 2))
						AS 'pago_restaurante',
					SUM(o.shipping_charge)
						AS 'envio',
					SUM(TRUNCATE(Round(( o.order_total ) * ( re.commission_percentage / 100 ), 2) , 2))
						AS 'comision_restaurante',
					SUM(TRUNCATE(Round(( o.shipping_charge ) * (0.30), 2) , 2))
						AS 'comision_envio',
					SUM(TRUNCATE(Round(( o.shipping_charge ) * (0.70), 2) , 2))
						AS 'comision_moto',
					SUM(o.credit_charge)
						AS 'cargo_tarjeta',
					SUM(o.tigo_money_charge)
						AS 'cargo_tigo',
			        SUM(( o.order_total + o.shipping_charge + o.credit_charge + o.tigo_money_charge ))
						AS 'total',
					count(if(os.order_status_id =12,'Entregada',os.order_status))
						AS 'estado'
			        FROM req_orders o 
		          	inner join req_order_ratings r 
			            ON o.order_id = r.order_id
			        inner join com_users u 
			            ON u.user_id = r.user_id 
			        inner join res_restaurants re 
			            ON re.restaurant_id = o.restaurant_id 
			        inner join res_service_types se 
			            ON se.service_type_id = o.service_type_id 
			        inner join req_order_status_logs l 
			            ON l.order_id = o.order_id 
			        inner join req_order_status os 
			            ON os.order_status_id = l.order_status_id 
			        inner join (SELECT order_id, 
			            	Max(order_status_id) AS order_status_id 
			              	FROM pf.req_order_status_logs where order_status_id = 5 OR order_status_id = 12
			              	GROUP  BY order_id) AS lot 
		                ON lot.order_id = o.order_id 
		              	AND lot.order_status_id = os.order_status_id 
	              	left join diner_addresses addr 
		                ON addr.address_id = o.address_id 
	              	left join com_zones ls 
		                ON ls.zone_id = addr.zone_id 
	              	left join req_orders_comment c 
		                ON o.order_id = c.order_id 
		            left join res_payment_methods m
			            ON o.payment_method_id = m.payment_method_id    
			        WHERE os.order_status != 'Demo' and u.email not like '%william.abarca01@gmail.com%'
			          	AND u.name not like '%PidaFacil%'  
			          	AND o.created_at BETWEEN '".$fecha1."' AND '".$fecha2."'
			          	and o.restaurant_id = '".$id_r."'
			        GROUP BY DAYOFMONTH(o.created_at) 
			        ORDER BY o.order_id;
		        ");
				
				$entregadas = DB::SELECT("
				SELECT 
		            o.order_cod 
		              	AS 'orden', 
		            Date_format(o.created_at, '%d/%m/%y %r') 
		              	AS 'fecha', 
		            if(os.order_status_id =12,'Entregada',os.order_status)
		              	AS 'estado'
		            FROM req_orders o 
	              	inner join req_order_ratings r 
		            	ON o.order_id = r.order_id
	              	inner join mensajero msj
		                ON o.order_id = msj.order_id
		            inner join motoristas moto
		                ON msj.motorista_id = moto.motorista_id
		            inner join com_users u 
		                ON u.user_id = r.user_id 
		            inner join res_restaurants re 
		                ON re.restaurant_id = o.restaurant_id 
		            inner join res_service_types se 
		                ON se.service_type_id = o.service_type_id 
		            inner join req_order_status_logs l 
		                ON l.order_id = o.order_id 
		            inner join req_order_status os 
		                ON os.order_status_id = l.order_status_id 
		            inner join (SELECT order_id, 
			                Max(order_status_id) AS order_status_id 
		                	FROM   pf.req_order_status_logs 
		                	GROUP  BY order_id) AS lot 
	                	ON lot.order_id = o.order_id 
		                AND lot.order_status_id = os.order_status_id 
	              	left join diner_addresses addr 
		                ON addr.address_id = o.address_id 
		            left join com_zones ls 
		                ON ls.zone_id = addr.zone_id 
		            left join req_orders_comment c 
		                ON o.order_id = c.order_id 
		            left join res_payment_methods m
		                ON o.payment_method_id = m.payment_method_id    
		            WHERE os.order_status != 'Demo' and u.email not like '%william.abarca01@gmail.com%'
		            AND u.name not like '%PidaFacil%'  
		            AND o.created_at BETWEEN '".$fecha1."' AND '".$fecha2."'
		            and o.restaurant_id = '".$id_r."'
		            GROUP  BY o.order_cod 
		            ORDER  BY o.order_id;
				");
	      	}else if($motivo == 'mes'){
	      		$mes = Input::get('mes');
	      		$anio = Input::get('anio');
	      		$restaurant = Input::get('rest');

	      		if($mes == 'todos'){
	      			$ordenes = DB::select("
			        	SELECT
			        	Date_format(o.created_at, '%m/%y')
							AS 'fecha',
						SUM(TRUNCATE(( o.order_total ) - ( ( o.order_total ) * Round(( re.commission_percentage / 100 ), 2) ), 2))
							AS 'pago_restaurante',
						SUM(o.shipping_charge)
							AS 'envio',
						SUM(TRUNCATE(Round(( o.order_total ) * ( re.commission_percentage / 100 ), 2) , 2))
							AS 'comision_restaurante',
						SUM(TRUNCATE(Round(( o.shipping_charge ) * (0.30), 2) , 2))
							AS 'comision_envio',
						SUM(TRUNCATE(Round(( o.shipping_charge ) * (0.70), 2) , 2))
							AS 'comision_moto',
						SUM(o.credit_charge)
							AS 'cargo_tarjeta',
						SUM(o.tigo_money_charge)
							AS 'cargo_tigo',
						SUM(( o.order_total + o.shipping_charge + o.credit_charge + o.tigo_money_charge ))
							AS 'total',
						count(if(os.order_status_id =12,'Entregada',os.order_status))
							AS 'estado'
						FROM req_orders o 
						inner join req_order_ratings r 
							ON o.order_id = r.order_id
						inner join com_users u 
							ON u.user_id = r.user_id 
						inner join res_restaurants re 
							ON re.restaurant_id = o.restaurant_id 
						inner join res_service_types se 
							ON se.service_type_id = o.service_type_id 
						inner join req_order_status_logs l 
							ON l.order_id = o.order_id 
						inner join req_order_status os 
							ON os.order_status_id = l.order_status_id 
						inner join (SELECT order_id, 
								Max(order_status_id) AS order_status_id 
								FROM pf.req_order_status_logs where order_status_id = 5 OR order_status_id = 12
								GROUP BY order_id) AS lot
							ON lot.order_id = o.order_id 
							AND lot.order_status_id = os.order_status_id 
						left join diner_addresses addr 
							ON addr.address_id = o.address_id 
						left join com_zones ls 
							ON ls.zone_id = addr.zone_id 
						left join req_orders_comment c 
							ON o.order_id = c.order_id 
						left join res_payment_methods m
							ON o.payment_method_id = m.payment_method_id    
						WHERE os.order_status != 'Demo' and u.email not like '%william.abarca01@gmail.com%'
							AND u.name not like '%PidaFacil%'
							AND Date_format(o.created_at, '%Y') = '".$anio."'
							and o.restaurant_id = '".$id_r."'
						GROUP BY MONTH(o.created_at)
						ORDER BY o.order_id
			        ");

					$entregadas = DB::SELECT("
					SELECT 
			            o.order_cod 
			              	AS 'orden', 
			            Date_format(o.created_at, '%m/%y')
			              	AS 'fecha', 
			            if(os.order_status_id =12,'Entregada',os.order_status)
			              	AS 'estado'
			            FROM req_orders o 
		              	inner join req_order_ratings r 
			            	ON o.order_id = r.order_id
		              	inner join mensajero msj
			                ON o.order_id = msj.order_id
			            inner join motoristas moto
			                ON msj.motorista_id = moto.motorista_id
			            inner join com_users u 
			                ON u.user_id = r.user_id 
			            inner join res_restaurants re 
			                ON re.restaurant_id = o.restaurant_id 
			            inner join res_service_types se 
			                ON se.service_type_id = o.service_type_id 
			            inner join req_order_status_logs l 
			                ON l.order_id = o.order_id 
			            inner join req_order_status os 
			                ON os.order_status_id = l.order_status_id 
			            inner join (SELECT order_id, 
				                Max(order_status_id) AS order_status_id 
			                	FROM   pf.req_order_status_logs 
			                	GROUP  BY order_id) AS lot 
		                	ON lot.order_id = o.order_id 
			                AND lot.order_status_id = os.order_status_id 
		              	left join diner_addresses addr 
			                ON addr.address_id = o.address_id 
			            left join com_zones ls 
			                ON ls.zone_id = addr.zone_id 
			            left join req_orders_comment c 
			                ON o.order_id = c.order_id 
			            left join res_payment_methods m
			                ON o.payment_method_id = m.payment_method_id    
			            WHERE os.order_status != 'Demo' and u.email not like '%william.abarca01@gmail.com%'
			            AND u.name not like '%PidaFacil%'  
			            AND Date_format(o.created_at, '%Y') = '".$anio."'
			            and o.restaurant_id = '".$id_r."'
			            GROUP BY o.order_cod 
			            ORDER BY o.order_id;
					");
	      		}else{
	      			$ordenes = DB::select("
			        	SELECT
			        	Date_format(o.created_at, '%d/%m/%y')
							AS 'fecha',
						SUM(TRUNCATE(( o.order_total ) - ( ( o.order_total ) * Round(( re.commission_percentage / 100 ), 2) ), 2))
							AS 'pago_restaurante',
						SUM(o.shipping_charge)
							AS 'envio',
						SUM(TRUNCATE(Round(( o.order_total ) * ( re.commission_percentage / 100 ), 2) , 2))
							AS 'comision_restaurante',
						SUM(TRUNCATE(Round(( o.shipping_charge ) * (0.30), 2) , 2))
							AS 'comision_envio',
						SUM(TRUNCATE(Round(( o.shipping_charge ) * (0.70), 2) , 2))
							AS 'comision_moto',
						SUM(o.credit_charge)
							AS 'cargo_tarjeta',
						SUM(o.tigo_money_charge)
							AS 'cargo_tigo',
				        SUM(( o.order_total + o.shipping_charge + o.credit_charge + o.tigo_money_charge ))
							AS 'total',
						count(if(os.order_status_id =12,'Entregada',os.order_status))
							AS 'estado'
				        FROM req_orders o 
			          	inner join req_order_ratings r 
				            ON o.order_id = r.order_id
				        inner join mensajero msj
				            ON o.order_id = msj.order_id
				        inner join motoristas moto
				            ON msj.motorista_id = moto.motorista_id
				        inner join com_users u 
				            ON u.user_id = r.user_id 
				        inner join res_restaurants re 
				            ON re.restaurant_id = o.restaurant_id 
				        inner join res_service_types se 
				            ON se.service_type_id = o.service_type_id 
				        inner join req_order_status_logs l 
				            ON l.order_id = o.order_id 
				        inner join req_order_status os 
				            ON os.order_status_id = l.order_status_id 
				        inner join (SELECT order_id, 
				            	Max(order_status_id) AS order_status_id 
				              	FROM pf.req_order_status_logs where order_status_id = 5 OR order_status_id = 12
				              	GROUP  BY order_id) AS lot 
			                ON lot.order_id = o.order_id 
			              	AND lot.order_status_id = os.order_status_id 
		              	left join diner_addresses addr 
			                ON addr.address_id = o.address_id 
		              	left join com_zones ls 
			                ON ls.zone_id = addr.zone_id 
		              	left join req_orders_comment c 
			                ON o.order_id = c.order_id 
			            left join res_payment_methods m
				            ON o.payment_method_id = m.payment_method_id    
				        WHERE os.order_status != 'Demo' and u.email not like '%william.abarca01@gmail.com%'
				          	AND u.name not like '%PidaFacil%'  
				          	AND Date_format(o.created_at, '%m') = '".$mes."'
				          	AND Date_format(o.created_at, '%Y') = '".$anio."'
				          	and o.restaurant_id = '".$id_r."'
				        GROUP BY DAY(o.created_at)
				        ORDER BY o.order_id;
			        ");

					$entregadas = DB::SELECT("
					SELECT 
			            o.order_cod 
			              	AS 'orden', 
			            Date_format(o.created_at, '%d/%m/%y')
			              	AS 'fecha', 
			            if(os.order_status_id =12,'Entregada',os.order_status)
			              	AS 'estado'
			            FROM req_orders o 
		              	inner join req_order_ratings r 
			            	ON o.order_id = r.order_id
		              	inner join mensajero msj
			                ON o.order_id = msj.order_id
			            inner join motoristas moto
			                ON msj.motorista_id = moto.motorista_id
			            inner join com_users u 
			                ON u.user_id = r.user_id 
			            inner join res_restaurants re 
			                ON re.restaurant_id = o.restaurant_id 
			            inner join res_service_types se 
			                ON se.service_type_id = o.service_type_id 
			            inner join req_order_status_logs l 
			                ON l.order_id = o.order_id 
			            inner join req_order_status os 
			                ON os.order_status_id = l.order_status_id 
			            inner join (SELECT order_id, 
				                Max(order_status_id) AS order_status_id 
			                	FROM   pf.req_order_status_logs 
			                	GROUP  BY order_id) AS lot 
		                	ON lot.order_id = o.order_id 
			                AND lot.order_status_id = os.order_status_id 
		              	left join diner_addresses addr 
			                ON addr.address_id = o.address_id 
			            left join com_zones ls 
			                ON ls.zone_id = addr.zone_id 
			            left join req_orders_comment c 
			                ON o.order_id = c.order_id 
			            left join res_payment_methods m
			                ON o.payment_method_id = m.payment_method_id    
			            WHERE os.order_status != 'Demo' and u.email not like '%william.abarca01@gmail.com%'
			            AND u.name not like '%PidaFacil%'  
			            AND Date_format(o.created_at, '%m') = '".$mes."'
				        AND Date_format(o.created_at, '%Y') = '".$anio."'
				        and o.restaurant_id = '".$id_r."'
			            GROUP  BY o.order_cod 
			            ORDER  BY o.order_id;
					");
	      		}
	      	}
	      	//Log::info($entregadas);
	  		return Response::json(array('ordenes'=>$ordenes,'entregadas'=>$entregadas));
	    }
  	}

  	public function generar_reporteD_rest(){
  		$user_id = Auth::id();
  		$restaurant_id = DB::table('res_user')
            ->select('restaurant_id')
            ->where('user_id',$user_id)
            ->get();
            
        $id_r = $restaurant_id[0]->restaurant_id;
  		if(Request::ajax()){

	      	$motivo = Input::get('motivo');

	      	if($motivo == 'fecha'){
		        $fecha1 = Input::get('fecha_inicio');
		        $fecha2 = Input::get('fecha_fin');

		        $ordenes = DB::select("
	          		SELECT
	          		o.order_cod, o.order_id,m.payment_method as 'tipo_pago',
	          		Date_format(o.created_at, '%d/%m/%y')
						AS 'fecha',
					SUM(TRUNCATE(( o.order_total ) - ( ( o.order_total ) * Round(( re.commission_percentage / 100 ), 2) ), 2))
						AS 'pago_restaurante',
					SUM(o.shipping_charge)
						AS 'envio',
					SUM(TRUNCATE(Round(( o.order_total ) * ( re.commission_percentage / 100 ), 2) , 2))
						AS 'comision_restaurante',
					SUM(TRUNCATE(Round(( o.shipping_charge ) * (0.30), 2) , 2))
						AS 'comision_envio',
					SUM(TRUNCATE(Round(( o.shipping_charge ) * (0.70), 2) , 2))
						AS 'comision_moto',
					SUM(o.credit_charge)
						AS 'cargo_tarjeta',
					SUM(o.tigo_money_charge)
						AS 'cargo_tigo',
			        SUM(( o.order_total + o.shipping_charge + o.credit_charge + o.tigo_money_charge ))
						AS 'total',
					if(os.order_status_id =12,'Entregada',os.order_status)
			              	AS 'estado',
					re.commission_percentage,
					ls.zone,
					max(l.created_at) as tiempoC,
					o.order_total,
					o.shipping_charge,
					o.pay_change
			        FROM req_orders o 
		          	inner join req_order_ratings r 
			            ON o.order_id = r.order_id
			        inner join com_users u 
			            ON u.user_id = r.user_id 
			        inner join res_restaurants re 
			            ON re.restaurant_id = o.restaurant_id 
			        inner join res_service_types se 
			            ON se.service_type_id = o.service_type_id 
			        inner join req_order_status_logs l 
			            ON l.order_id = o.order_id 
			        inner join req_order_status os 
			            ON os.order_status_id = l.order_status_id 
			        inner join (SELECT order_id, 
			            	Max(order_status_id) AS order_status_id 
			              	FROM pf.req_order_status_logs where order_status_id = 5 OR order_status_id = 12
			              	GROUP  BY order_id) AS lot 
		                ON lot.order_id = o.order_id 
		              	AND lot.order_status_id = os.order_status_id 
	              	left join diner_addresses addr 
		                ON addr.address_id = o.address_id 
	              	left join com_zones ls 
		                ON ls.zone_id = addr.zone_id 
	              	left join req_orders_comment c 
		                ON o.order_id = c.order_id 
		            left join res_payment_methods m
			            ON o.payment_method_id = m.payment_method_id    
			        WHERE os.order_status != 'Demo' and u.email not like '%william.abarca01@gmail.com%'
			          	AND u.name not like '%PidaFacil%'  
			          	AND o.created_at BETWEEN '".$fecha1."' AND '".$fecha2."'
			          	and o.restaurant_id = '".$id_r."'
			        GROUP BY DAYOFMONTH(o.created_at) 
			        ORDER BY o.order_id;
	        	");
	      	}

	      Log::info($ordenes);
	      return Response::json($ordenes);
	    }
  	}

  	public function generarReporteT_rest(){
  		$user_id = Auth::id();
  		$restaurant_id = DB::table('res_user')
            ->select('restaurant_id')
            ->where('user_id',$user_id)
            ->get();
            
        $id_r = $restaurant_id[0]->restaurant_id;
  		if(Request::ajax()){

	      	$motivo = Input::get('motivo');

	      	if($motivo == 'fecha'){
		        $fecha1 = Input::get('fecha_inicio');
		        $fecha2 = Input::get('fecha_fin');

		        $ordenes = DB::select("
	          		SELECT
	          		o.order_id,					
					Date_format(o.created_at, '%H:%i:%s')
						AS 'hora_creacion',					
					Date_format(mens.created_at, '%H:%i:%s')
						AS 'hora_cambio',
					o.order_cod
						AS 'orden',
					mens.mensajero_status_id
						AS 'status_id',
					meSt.mensajero_status
						AS 'status'
					FROM req_orders o
					inner join mensajero_status_log mens
						ON o.order_id = mens.order_id
					inner join mensajero_status meSt
						ON mens.mensajero_status_id = meSt.mensajero_status_id
					inner join req_order_ratings r 
						ON o.order_id = r.order_id
					inner join com_users u 
						ON u.user_id = r.user_id 
					inner join res_restaurants re 
						ON re.restaurant_id = o.restaurant_id 
					inner join res_service_types se 
						ON se.service_type_id = o.service_type_id 
					inner join req_order_status_logs l 
						ON l.order_id = o.order_id 
					inner join req_order_status os 
						ON os.order_status_id = l.order_status_id 
					inner join (SELECT order_id, 
							Max(order_status_id) AS order_status_id 
							FROM pf.req_order_status_logs where order_status_id = 5 OR order_status_id = 12
							GROUP  BY order_id) AS lot 
						ON lot.order_id = o.order_id 
						AND lot.order_status_id = os.order_status_id 
					left join diner_addresses addr 
						ON addr.address_id = o.address_id 
					left join com_zones ls 
						ON ls.zone_id = addr.zone_id 
					left join req_orders_comment c 
						ON o.order_id = c.order_id 
					left join res_payment_methods m
						ON o.payment_method_id = m.payment_method_id    
					WHERE os.order_status != 'Demo' and u.email not like '%william.abarca01@gmail.com%'
						AND u.name not like '%PidaFacil%' 
			          	AND o.created_at BETWEEN '".$fecha1."' AND '".$fecha2."'
			          	and o.restaurant_id = '".$id_r."'
			          	and mens.mensajero_status_id IN(5,6,8)
			        ORDER BY o.order_id, mens.mensajero_status_id;
	        	");
	      	}

	      return Response::json($ordenes);
	    }
  	}

}