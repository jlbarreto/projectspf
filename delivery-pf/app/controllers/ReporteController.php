<?php

class ReporteController extends \BaseController {

	public function vista_rv(){
	    $motoristas = DB::select('select * from motoristas LIMIT 10');
	    $res = Restaurant::get();
	    $orders = Order::where('service_type_id', 3)
	              ->where('viewed_pidafacil',1)
	              ->get();

	    $condi = DB::table("res_conditions")->get();

	    return View::make('web.ventas')
	    ->with('motoristas', $motoristas);
  	}

  	public function reporte_ventas(){

	    if(Request::ajax()){

	      	$motivo = Input::get('motivo');

	      	if($motivo == 'fecha'){
		        $fecha1 = Input::get('fecha_inicio');
		        $fecha2 = Input::get('fecha_fin');

		        $ordenes = DB::select("
		          SELECT u.name 
		            AS 'nombre_cliente', 
		          u.last_name 
		            AS 'apellido_cliente', 
		          u.email 
		            AS 'email', 
		          o.order_cod 
		            AS 'orden', 
		          Date_format(o.created_at, '%d/%m/%y %r') 
		            AS 'fecha', 
		          re.name 
		            AS 'restaurante', 
		          se.service_type 
		            AS 'tipo_servicio', 
		          if(os.order_status_id =12,'Entregada',os.order_status)
		            AS 'estado', 
		          o.source_device as 'origen',
		          m.payment_method as 'tipo_pago',
		          o.order_total 
		            AS 'subtotal', 
		          o.shipping_charge 
		            AS 'costo_envio', 
		          o.credit_charge 
		            AS 'cargo_tarjeta', 
		          ( o.order_total + o.shipping_charge + o.credit_charge ) 
		            AS 'total', 
		          TRUNCATE(( o.order_total ) - ( ( o.order_total ) * Round(( re.commission_percentage / 100 ), 2) ), 2) 
		            AS 'pago_restaurante', 
		           TRUNCATE(Round(( o.order_total ) * ( re.commission_percentage / 100 ), 2) , 2) 
		            AS 'comision_pf', 
		          msj.motorista_id 
		            AS 'motociclista_id',
		          moto.nombre
		            AS 'motociclista', 
		          ifnull(ls.zone,'') 
		            AS 'zona', 
		          Ifnull(c.motivo, '') 
		            AS 'motivo_observacion', 
		          Ifnull(c.COMMENT, '') 
		            AS 'comentario_observacion', 
		          Ifnull(c.user_name, '') 
		            AS 'usuario_observacion' 
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
		          GROUP  BY o.order_cod 
		          ORDER  BY o.order_id; 
		        ");
	      	}elseif($motivo == 'moto'){
		        $moto = Input::get('motorista');
		        $date = new DateTime();
		        $fecha = $date->format('Y-m-d');

		        if($moto == 'todos'){
		          	$ordenes = DB::select("
			            SELECT u.name 
			              AS 'nombre_cliente', 
			            u.last_name 
			              AS 'apellido_cliente', 
			            u.email 
			              AS 'email', 
			            o.order_cod 
			              AS 'orden', 
			            Date_format(o.created_at, '%d/%m/%y %r') 
			              AS 'fecha', 
			            re.name 
			              AS 'restaurante', 
			            se.service_type 
			              AS 'tipo_servicio', 
			            if(os.order_status_id =12,'Entregada',os.order_status)
			              AS 'estado', 
			            o.source_device as 'origen',
			            m.payment_method as 'tipo_pago',
			            o.order_total 
			              AS 'subtotal', 
			            o.shipping_charge 
			              AS 'costo_envio', 
			            o.credit_charge 
			              AS 'cargo_tarjeta', 
			            ( o.order_total + o.shipping_charge + o.credit_charge ) 
			              AS 'total', 
			            TRUNCATE(( o.order_total ) - ( ( o.order_total ) * Round(( re.commission_percentage / 100 ), 2) ), 2) 
			              AS 'pago_restaurante', 
			             TRUNCATE(Round(( o.order_total ) * ( re.commission_percentage / 100 ), 2) , 2) 
			              AS 'comision_pf', 
			            msj.motorista_id 
			              AS 'motociclista_id',
			            moto.nombre
			              AS 'motociclista', 
			            ifnull(ls.zone,'') 
			              AS 'zona', 
			            Ifnull(c.motivo, '') 
			              AS 'motivo_observacion', 
			            Ifnull(c.COMMENT, '') 
			              AS 'comentario_observacion', 
			            Ifnull(c.user_name, '') 
			              AS 'usuario_observacion' 
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
			            AND o.created_at LIKE '%".$fecha."%'
			            GROUP  BY o.order_cod 
			            ORDER  BY o.order_id; 
		        	");
	        	}else{
	          		$ordenes = DB::select("
			            SELECT u.name 
			            	AS 'nombre_cliente', 
			            u.last_name 
			            	AS 'apellido_cliente', 
			            u.email 
			              	AS 'email', 
			            o.order_cod 
			              	AS 'orden', 
			            Date_format(o.created_at, '%d/%m/%y %r') 
			              	AS 'fecha', 
			            re.name 
			              	AS 'restaurante', 
			            se.service_type 
			              	AS 'tipo_servicio', 
			            if(os.order_status_id =12,'Entregada',os.order_status)
			              	AS 'estado', 
			            o.source_device as 'origen',
			            m.payment_method as 'tipo_pago',
			            o.order_total 
			              	AS 'subtotal', 
			            o.shipping_charge 
			              	AS 'costo_envio', 
			            o.credit_charge 
			              	AS 'cargo_tarjeta', 
			            (o.order_total + o.shipping_charge + o.credit_charge ) 
			              	AS 'total', 
			            TRUNCATE(( o.order_total ) - ( ( o.order_total ) * Round(( re.commission_percentage / 100 ), 2) ), 2) 
			              	AS 'pago_restaurante', 
			            TRUNCATE(Round(( o.order_total ) * ( re.commission_percentage / 100 ), 2) , 2) 
			              	AS 'comision_pf', 
			            msj.motorista_id 
			              	AS 'motociclista_id',
			            moto.nombre
			              	AS 'motociclista', 
			            ifnull(ls.zone,'') 
			              	AS 'zona', 
			            Ifnull(c.motivo, '') 
			              	AS 'motivo_observacion', 
			            Ifnull(c.COMMENT, '') 
			              	AS 'comentario_observacion', 
			            Ifnull(c.user_name, '') 
			              	AS 'usuario_observacion' 
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
			            inner join mensajero as rm 
			                ON o.order_id = rm.order_id
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
			            AND rm.motorista_id = '".$moto."' AND o.created_at LIKE '%".$fecha."%'
			            GROUP  BY o.order_cod 
			            ORDER  BY o.order_id; 
	          		");
	        	}
	      	}elseif($motivo == 'pago'){
		        $tipo_pago = Input::get('pago');
		        $date = new DateTime();
		        $fecha = $date->format('Y-m-d');

	        	if($tipo_pago == 'todos'){
	          		$ordenes = DB::select("
			            SELECT u.name 
			              	AS 'nombre_cliente', 
			            u.last_name 
			              	AS 'apellido_cliente', 
			            u.email 
			              	AS 'email', 
			            o.order_cod 
			              	AS 'orden', 
			            Date_format(o.created_at, '%d/%m/%y %r') 
			              	AS 'fecha', 
			            re.name 
			              	AS 'restaurante', 
			            se.service_type 
			              	AS 'tipo_servicio', 
			            if(os.order_status_id =12,'Entregada',os.order_status)
			              	AS 'estado', 
			            o.source_device as 'origen',
			            m.payment_method as 'tipo_pago',
			            o.order_total 
			              	AS 'subtotal', 
			            o.shipping_charge 
			              	AS 'costo_envio', 
			            o.credit_charge 
			              	AS 'cargo_tarjeta', 
			            ( o.order_total + o.shipping_charge + o.credit_charge ) 
			              	AS 'total', 
			            TRUNCATE(( o.order_total ) - ( ( o.order_total ) * Round(( re.commission_percentage / 100 ), 2) ), 2) 
			              	AS 'pago_restaurante', 
			             TRUNCATE(Round(( o.order_total ) * ( re.commission_percentage / 100 ), 2) , 2) 
			              	AS 'comision_pf', 
			            msj.motorista_id 
			              	AS 'motociclista_id',
			            moto.nombre
			              	AS 'motociclista', 
			            ifnull(ls.zone,'') 
			              	AS 'zona', 
			            Ifnull(c.motivo, '') 
			              	AS 'motivo_observacion', 
			            Ifnull(c.COMMENT, '') 
			              	AS 'comentario_observacion', 
			            Ifnull(c.user_name, '') 
			              	AS 'usuario_observacion' 
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
			              AND o.created_at LIKE '%".$fecha."%'
			            GROUP  BY o.order_cod 
			            ORDER  BY o.order_id; 
	          		");
	        	}else{
	         		$ordenes = DB::select("
			            SELECT u.name 
			              	AS 'nombre_cliente', 
			            u.last_name 
			              	AS 'apellido_cliente', 
			            u.email 
			              	AS 'email', 
			            o.order_cod 
			              	AS 'orden', 
			            Date_format(o.created_at, '%d/%m/%y %r') 
			              	AS 'fecha', 
			            re.name 
			              	AS 'restaurante', 
			            se.service_type 
			              	AS 'tipo_servicio', 
			            if(os.order_status_id =12,'Entregada',os.order_status)
			              	AS 'estado', 
			            o.source_device as 'origen',
			            m.payment_method as 'tipo_pago',
			            o.order_total 
			              	AS 'subtotal', 
			            o.shipping_charge 
			              	AS 'costo_envio', 
			            o.credit_charge 
			              	AS 'cargo_tarjeta', 
			            ( o.order_total + o.shipping_charge + o.credit_charge ) 
			              	AS 'total', 
			            TRUNCATE(( o.order_total ) - ( ( o.order_total ) * Round(( re.commission_percentage / 100 ), 2) ), 2) 
			              	AS 'pago_restaurante', 
			             TRUNCATE(Round(( o.order_total ) * ( re.commission_percentage / 100 ), 2) , 2) 
			              	AS 'comision_pf', 
			            msj.motorista_id 
			              	AS 'motociclista_id',
			            moto.nombre
			              	AS 'motociclista', 
			            ifnull(ls.zone,'') 
			              	AS 'zona', 
			            Ifnull(c.motivo, '') 
			              	AS 'motivo_observacion', 
			            Ifnull(c.COMMENT, '') 
			              	AS 'comentario_observacion', 
			            Ifnull(c.user_name, '') 
			              	AS 'usuario_observacion' 
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
			            AND o.payment_method_id = '".$tipo_pago."' AND o.created_at LIKE '%".$fecha."%'
			            GROUP  BY o.order_cod 
			            ORDER  BY o.order_id; 
	          		");
	        	}
	      	}

	      Log::info($ordenes);
	      return Response::json($ordenes);
	    }
  	}

  	public function ventaEj(){
  		$restaurantes = DB::select('
			select * from res_restaurants 
			where activate = 1 
			order by name asc
		');

    	return View::make('web.ventas_new')
    		->with('restaurantes', $restaurantes);
  	}

  	public function ventasReporte(){

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
		            GROUP  BY o.order_cod 
		            ORDER  BY o.order_id;
				");
	      	}else if($motivo == 'mes'){
	      		$mes = Input::get('mes');
	      		$anio = Input::get('anio');
	      		$restaurant = Input::get('rest');

	      		if($mes == 'todos' && $restaurant == 'todos'){
	      			$ordenes = DB::select("
			        	SELECT Date_format(o.created_at, '%m/%y') 
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
						GROUP BY MONTH(o.created_at)
						ORDER BY o.order_id
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
			            AND Date_format(o.created_at, '%Y') = '".$anio."'
			            GROUP BY o.order_cod 
			            ORDER BY o.order_id;
					");
	      		}elseif($mes == 'todos' && $restaurant != 'todos'){
	      			$ordenes = DB::select("
			        	SELECT Date_format(o.created_at, '%m/%y')
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
				          	AND o.restaurant_id = '".$restaurant."'
				          	AND Date_format(o.created_at, '%Y') = '".$anio."'
						GROUP BY MONTH(o.created_at)
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
			            AND o.restaurant_id = '".$restaurant."'
			          	AND Date_format(o.created_at, '%Y') = '".$anio."'
			            GROUP  BY o.order_cod 
			            ORDER  BY o.order_id;
					");
	      		}elseif($mes != 'todos' && $restaurant == 'todos'){
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
				          	AND Date_format(o.created_at, '%m') = '".$mes."'
				          	AND Date_format(o.created_at, '%Y') = '".$anio."'
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
			            AND Date_format(o.created_at, '%m') = '".$mes."'
				        AND Date_format(o.created_at, '%Y') = '".$anio."'
			            GROUP  BY o.order_cod 
			            ORDER  BY o.order_id;
					");
	      		}elseif($mes != 'todos' && $restaurant != 'todos'){
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
				          	AND o.restaurant_id = '".$restaurant."'
				          	AND Date_format(o.created_at, '%m') = '".$mes."'
				          	AND Date_format(o.created_at, '%Y') = '".$anio."'
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
			            AND o.restaurant_id = '".$restaurant."'
			          	AND Date_format(o.created_at, '%m') = '".$mes."'
			          	AND Date_format(o.created_at, '%Y') = '".$anio."'
			            GROUP  BY o.order_cod 
			            ORDER  BY o.order_id;
					");
	      		}else{
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
				        GROUP BY MONTH(o.created_at)
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
			            AND Date_format(o.created_at, '%m') = '".$mes."'
				        AND Date_format(o.created_at, '%Y') = '".$anio."'
			            GROUP  BY o.order_cod 
			            ORDER  BY o.order_id;
					");
	      		}
	      	}
	      	//Log::info($entregadas);
	  		return Response::json(array('ordenes'=>$ordenes,'entregadas'=>$entregadas));
	    }
	}

	public function reporteExcel(){

  		$motivo = Input::get('motivo2');
  		$date = new DateTime();
  		$fechaR = $date->format('d-m-Y');

      	if($motivo == 'fecha'){
	        $fecha1 = Input::get('fechaN1');
	        $fecha2 = Input::get('fechaN2');

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
		        GROUP BY DAYOFMONTH(o.created_at) 
		        ORDER BY o.order_id;
	        ");
			
			#Generamos el csv
			$nombreArchivo = "ReporteFechas_$fechaR.csv";
			$file = fopen("Reportes/".$nombreArchivo,"w");
			$end="
			";
			$sep=",";
			$tags = "";
			$data = "";

			#se genera tags
			$tags.="Fecha".$sep;
			$tags.="Pago Restaurante".$sep;
			$tags.="Costo Envio".$sep;
			$tags.="Comision Restaurante".$sep;
			$tags.="Comision Envio".$sep;
			$tags.="Total Venta".$sep;
			$tags.="Num. Pedidos".$end;

			fwrite($file,$tags);

			foreach ($ordenes as $key => $row) {

				$data.=$row->fecha.$sep;
				$data.=$row->pago_restaurante.$sep;
				$data.=$row->envio.$sep;
				$data.=$row->comision_restaurante.$sep;
				$data.=$row->comision_envio.$sep;
				$data.=$row->total.$sep;
				$data.=$row->estado.$end;

				fwrite($file,$data);
				$data="";
			}

			fclose($file);

			header("Content-disposition: attachment; filename=$nombreArchivo");
			header("Content-type: application/octet-stream");
			
			readfile("Reportes/".$nombreArchivo);	

			unlink("Reportes/".$nombreArchivo);
			
			//Log::info($ordenes);
      	}else if($motivo == 'mes'){
      		$mes = Input::get('mesN');
      		$restaurant = Input::get('RestN');
      		$anio = Input::get('AnioN');

	        if($mes == 'todos' && $restaurant == 'todos'){
      			$ordenes = DB::select("
		        	SELECT Date_format(o.created_at, '%m/%y') 
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
					GROUP BY MONTH(o.created_at)
					ORDER BY o.order_id
		        ");
      		}elseif($mes == 'todos' && $restaurant != 'todos'){
      			$ordenes = DB::select("
		        	SELECT Date_format(o.created_at, '%m/%y')
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
			          	AND o.restaurant_id = '".$restaurant."'
			          	AND Date_format(o.created_at, '%Y') = '".$anio."'
			        GROUP BY MONTH(o.created_at)
			        ORDER BY o.order_id;
		        ");
      		}elseif($mes != 'todos' && $restaurant == 'todos'){
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
			          	AND Date_format(o.created_at, '%m') = '".$mes."'
			          	AND Date_format(o.created_at, '%Y') = '".$anio."'
			        GROUP BY DAYOFMONTH(o.created_at)
			        ORDER BY o.order_id;
		        ");
      		}elseif($mes != 'todos' && $restaurant != 'todos'){
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
			          	AND o.restaurant_id = '".$restaurant."'
			          	AND Date_format(o.created_at, '%m') = '".$mes."'
			          	AND Date_format(o.created_at, '%Y') = '".$anio."'
			        GROUP BY DAYOFMONTH(o.created_at)
			        ORDER BY o.order_id;
		        ");
      		}else{
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
			          	AND Date_format(o.created_at, '%m') = '".$mes."'
			          	AND Date_format(o.created_at, '%Y') = '".$anio."'
			        GROUP BY DAYOFMONTH(o.created_at) 
			        ORDER BY o.order_id;
		        ");
      		}

			#Generamos el csv
			$nombreArchivo = "ReporteMes_$fechaR.csv";
			$file = fopen("Reportes/".$nombreArchivo,"w");
			$end="
			";
			$sep=",";
			$tags = "";
			$data = "";

			#se genera tags
			$tags.="Fecha".$sep;
			$tags.="Pago Restaurante".$sep;
			$tags.="Costo Envio".$sep;
			$tags.="Comision Restaurante".$sep;
			$tags.="Comision Envio".$sep;
			$tags.="Total Venta".$sep;
			$tags.="Num. Pedidos".$end;

			fwrite($file,$tags);

			foreach ($ordenes as $key => $row) {

				$data.=$row->fecha.$sep;
				$data.=$row->pago_restaurante.$sep;
				$data.=$row->envio.$sep;
				$data.=$row->comision_restaurante.$sep;
				$data.=$row->comision_envio.$sep;
				$data.=$row->total.$sep;
				$data.=$row->estado.$end;

				fwrite($file,$data);
				$data="";
			}

			fclose($file);

			header("Content-disposition: attachment; filename=$nombreArchivo");
			header("Content-type: application/octet-stream");
			
			readfile("Reportes/".$nombreArchivo);	

			unlink("Reportes/".$nombreArchivo);
      	}
	}

	public function reporteExcelDet(){
		$motivo = Input::get('motivo2');
  		$date = new DateTime();
  		$fechaR = $date->format('d-m-Y');

      	if($motivo == 'fecha'){
	        $fecha1 = Input::get('fechaN1');
	        $fecha2 = Input::get('fechaN2');

	        $ordenes = DB::select("
		        SELECT u.name 
	            	AS 'nombre_cliente',
	          	u.last_name 
	            	AS 'apellido_cliente',
		        u.email 
	            	AS 'email',
	          	o.order_cod 
		            AS 'orden',
	          	Date_format(o.created_at, '%d/%m/%y %r') 
		            AS 'fecha', 
	          	re.name 
		            AS 'restaurante',
	          	se.service_type
		            AS 'tipo_servicio',
	          	if(os.order_status_id =12,'Entregada',os.order_status)
		            AS 'estado',
	          	o.source_device as 'origen',
		        m.payment_method as 'tipo_pago',
		        o.order_total
	            	AS 'subtotal',
	          	o.shipping_charge
	            	AS 'costo_envio',
	          	o.credit_charge
	            	AS 'cargo_tarjeta',
	          	(o.order_total + o.shipping_charge + o.credit_charge) 
		            AS 'total', 
	          	TRUNCATE(( o.order_total ) - ( ( o.order_total ) * Round(( re.commission_percentage / 100 ), 2) ), 2) 
		            AS 'pago_restaurante', 
	           	TRUNCATE(Round(( o.order_total ) * ( re.commission_percentage / 100 ), 2) , 2) 
		            AS 'comision_pf', 
	          	msj.motorista_id 
		            AS 'motociclista_id',
	          	moto.nombre
		            AS 'motociclista', 
		        ifnull(ls.zone,'') 
		            AS 'zona', 
		        Ifnull(c.motivo, '') 
		            AS 'motivo_observacion', 
		        Ifnull(c.COMMENT, '') 
		            AS 'comentario_observacion', 
		        Ifnull(c.user_name, '') 
		            AS 'usuario_observacion' 
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
		        GROUP  BY o.order_cod 
		        ORDER  BY o.order_id; 
		    ");
			
			#Generamos el csv
			$nombreArchivo = "ReporteFechas_$fechaR.csv";
			$file = fopen("Reportes/".$nombreArchivo,"w");
			$end="
			";
			$sep=",";
			$tags = "";
			$data = "";

			#se genera tags
			$tags.="NOMBRE CLIENTE".$sep;
			$tags.="APELLIDO CLIENTE".$sep;
			$tags.="EMAIL".$sep;
			$tags.="ORDEN".$sep;
			$tags.="FECHA".$sep;
			$tags.="RESTAURANTE".$sep;
			$tags.="TIPO DE SERVICIO".$sep;
			$tags.="ESTADO".$sep;
			$tags.="ORIGEN".$sep;
			$tags.="TIPO PAGO".$sep;
			$tags.="SUBTOTAL".$sep;
			$tags.="COSTO ENVIO".$sep;
			$tags.="CARGO TARJETA".$sep;
			$tags.="TOTAL".$sep;
			$tags.="PAGO RESTAURANTE".$sep;
			$tags.="COMISION PIDAFACIL".$sep;
			$tags.="MOTOCICLISTA".$sep;
			$tags.="ZONA".$sep;
			$tags.="MOTIVO OBSERVACION".$sep;
			$tags.="COMENTARIO OBSERVACION".$end;

			fwrite($file,$tags);

			foreach ($ordenes as $key => $row) {

				$data.=$row->nombre_cliente.$sep;
				$data.=$row->apellido_cliente.$sep;
				$data.=$row->email.$sep;
				$data.=$row->orden.$sep;
				$data.=$row->fecha.$sep;
				$data.=$row->restaurante.$sep;
				$data.=$row->tipo_servicio.$sep;
				$data.=$row->estado.$sep;
				$data.=$row->origen.$sep;
				$data.=$row->tipo_pago.$sep;
				$data.=$row->subtotal.$sep;
				$data.=$row->costo_envio.$sep;
				$data.=$row->cargo_tarjeta.$sep;
				$data.=$row->total.$sep;
				$data.=$row->pago_restaurante.$sep;
				$data.=$row->comision_pf.$sep;
				$data.=$row->motociclista.$sep;
				$data.=$row->zona.$sep;
				$data.=$row->motivo_observacion.$sep;
				$data.=$row->comentario_observacion.$end;

				fwrite($file,$data);
				$data="";
			}

			fclose($file);

			header("Content-disposition: attachment; filename=$nombreArchivo");
			header("Content-type: application/octet-stream");
			
			readfile("Reportes/".$nombreArchivo);	

			unlink("Reportes/".$nombreArchivo);
		}
	}

	public function viewIva(){

		$restaurantes = DB::select('
			select * from res_restaurants 
			where activate = 1 
			order by name asc
		');

		return View::make('web.reporte_iva')
			->with('restaurantes', $restaurantes);
	}

	public function reporteIva(){

		if(Request::ajax()){
			$motivo = Input::get('motivo');
			if($motivo == 'fecha'){
		        $fecha1 = Input::get('fecha_inicio');
		        $fecha2 = Input::get('fecha_fin');

		        $ordenes = DB::select("
		        	SELECT re.name AS 'restaurante',
						SUM(TRUNCATE(( o.order_total ) - ( ( o.order_total ) * Round(( re.commission_percentage / 100 ), 2) ), 2))
							AS 'pago_restaurante',
						Date_format(o.created_at, '%d/%m/%y') 
							AS 'fecha'
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
			        GROUP BY o.restaurant_id, Date_format(o.created_at, '%m')
			        ORDER BY o.order_id;
		        ");
							
				//Log::info($ordenes);
	      	}else if($motivo == 'mes'){
	      		$mes = Input::get('mes');
	      		//$restaurant = Input::get('RestN');
	      		$anio = Input::get('anio');
		        $rest = Input::get('rest');

	        if($mes == 'todos' && $rest == 'todos'){
	        	Log::info('Entro al 1');
      			$ordenes = DB::select("
		        	SELECT Date_format(o.created_at, '%m/%y') 
						AS 'fecha',
					SUM(TRUNCATE(( o.order_total ) - ( ( o.order_total ) * Round(( re.commission_percentage / 100 ), 2) ), 2))
						AS 'pago_restaurante',
					re.name AS 'restaurante'
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
					GROUP BY o.restaurant_id, Date_format(o.created_at, '%m')
					ORDER BY o.order_id
		        ");
				Log::info($mes.' '.$anio);
      		}elseif($mes == 'todos' && $rest != 'todos'){
      			Log::info('Entro al 2');
      			$ordenes = DB::select("
		        	SELECT Date_format(o.created_at, '%m/%y') 
						AS 'fecha',
					SUM(TRUNCATE(( o.order_total ) - ( ( o.order_total ) * Round(( re.commission_percentage / 100 ), 2) ), 2))
						AS 'pago_restaurante',
					re.name AS 'restaurante'
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
						AND Date_format(o.created_at, '%Y') = '".$anio."'
						AND o.restaurant_id = '".$rest."'
					GROUP BY o.restaurant_id, Date_format(o.created_at, '%m')
					ORDER BY o.order_id
		        ");
				Log::info($mes.' '.$anio);
      		}elseif($mes != 'todos' && $rest == 'todos'){
      			Log::info('Entro al 3');
      			$ordenes = DB::select("
		        	SELECT Date_format(o.created_at, '%m/%y') 
						AS 'fecha',
					SUM(TRUNCATE(( o.order_total ) - ( ( o.order_total ) * Round(( re.commission_percentage / 100 ), 2) ), 2))
						AS 'pago_restaurante',
					re.name AS 'restaurante'
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
			          	AND Date_format(o.created_at, '%m') = '".$mes."'
			          	AND Date_format(o.created_at, '%Y') = '".$anio."'
			        GROUP BY o.restaurant_id
			        ORDER BY o.order_id;
		        ");
				Log::info($mes.' '.$anio);
      		}elseif($mes != 'todos' && $rest != 'todos'){
      			Log::info('Entro al 4');
      			$ordenes = DB::select("
		        	SELECT Date_format(o.created_at, '%m/%y') 
						AS 'fecha',
					SUM(TRUNCATE(( o.order_total ) - ( ( o.order_total ) * Round(( re.commission_percentage / 100 ), 2) ), 2))
						AS 'pago_restaurante',
					re.name AS 'restaurante'
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
			          	AND Date_format(o.created_at, '%m') = '".$mes."'
			          	AND Date_format(o.created_at, '%Y') = '".$anio."'
			          	AND o.restaurant_id = '".$rest."'
			        GROUP BY o.restaurant_id
			        ORDER BY o.order_id;
		        ");
				Log::info($mes.' '.$anio);
      		}
	      	}
	    	return Response::json(array('ordenes'=>$ordenes));
		}
	}

	//Exportar a excel reporte de iva
	public function excelIva(){

  		$motivo = Input::get('motivoiva');
  		$date = new DateTime();
  		$fechaR = $date->format('d-m-Y');

      	if($motivo == 'fecha'){
	        $fecha1 = Input::get('fechaiva1');
	        $fecha2 = Input::get('fechaiva2');

	        Log::info($fecha1.' '.$fecha2);

	        $ordenes = DB::select("
	        	SELECT re.name AS 'restaurante',
					SUM(TRUNCATE(( o.order_total ) - ( ( o.order_total ) * Round(( re.commission_percentage / 100 ), 2) ), 2))
						AS 'pago_restaurante',
					Date_format(o.created_at, '%d/%m/%y') 
						AS 'fecha'
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
		        GROUP BY o.restaurant_id, Date_format(o.created_at, '%m')
		        ORDER BY o.order_id;
	        ");
			
			#Generamos el csv
			$nombreArchivo = "ReporteIva_$fechaR.csv";
			$file = fopen("Reportes/".$nombreArchivo,"w");
			$end="
			";
			$sep=",";
			$tags = "";
			$data = "";

			#se genera tags
			$tags.="Fecha".$sep;
			$tags.="Restaurante".$sep;
			$tags.="Monto total pagado".$sep;
			$tags.="Iva a cobrar".$sep;			
			$tags.="Valor restaurante".$end;

			fwrite($file,$tags);

			foreach ($ordenes as $key => $row) {

				$totalIva = round($row->pago_restaurante * 0.13, 2);
				$totalSinIva = round($row->pago_restaurante / 1.13, 2);

				$data.= $row->fecha.$sep;
				$data.= $row->restaurante.$sep;
				$data.= $row->pago_restaurante.$sep;
				$data.= $totalIva.$sep;
				$data.= $totalSinIva.$end;

				fwrite($file,$data);
				$data="";
			}

			fclose($file);

			header("Content-disposition: attachment; filename=$nombreArchivo");
			header("Content-type: application/octet-stream");
			
			readfile("Reportes/".$nombreArchivo);	

			unlink("Reportes/".$nombreArchivo);
			
			//Log::info($ordenes);
      	}else if($motivo == 'mes'){
      		$mes = Input::get('mesiva');      		
      		$anio = Input::get('anioiva');
      		$rest = Input::get('restiva');

	        if($mes == 'todos' && $rest == 'todos'){
	        	Log::info('Entro al 1');
      			$ordenes = DB::select("
		        	SELECT Date_format(o.created_at, '%m/%y') 
						AS 'fecha',
					SUM(TRUNCATE(( o.order_total ) - ( ( o.order_total ) * Round(( re.commission_percentage / 100 ), 2) ), 2))
						AS 'pago_restaurante',
					re.name AS 'restaurante'
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
					GROUP BY o.restaurant_id, Date_format(o.created_at, '%m')
					ORDER BY o.order_id
		        ");
				Log::info($mes.' '.$anio);
      		}elseif($mes == 'todos' && $rest != 'todos'){
      			Log::info('Entro al 2');
      			$ordenes = DB::select("
		        	SELECT Date_format(o.created_at, '%m/%y') 
						AS 'fecha',
					SUM(TRUNCATE(( o.order_total ) - ( ( o.order_total ) * Round(( re.commission_percentage / 100 ), 2) ), 2))
						AS 'pago_restaurante',
					re.name AS 'restaurante'
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
						AND Date_format(o.created_at, '%Y') = '".$anio."'
						AND o.restaurant_id = '".$rest."'
					GROUP BY o.restaurant_id, Date_format(o.created_at, '%m')
					ORDER BY o.order_id
		        ");
				Log::info($mes.' '.$anio);
      		}elseif($mes != 'todos' && $rest == 'todos'){
      			Log::info('Entro al 3');
      			$ordenes = DB::select("
		        	SELECT Date_format(o.created_at, '%m/%y') 
						AS 'fecha',
					SUM(TRUNCATE(( o.order_total ) - ( ( o.order_total ) * Round(( re.commission_percentage / 100 ), 2) ), 2))
						AS 'pago_restaurante',
					re.name AS 'restaurante'
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
			          	AND Date_format(o.created_at, '%m') = '".$mes."'
			          	AND Date_format(o.created_at, '%Y') = '".$anio."'
			        GROUP BY o.restaurant_id
			        ORDER BY o.order_id;
		        ");
				Log::info($mes.' '.$anio);
      		}elseif($mes != 'todos' && $rest != 'todos'){
      			Log::info('Entro al 4');
      			$ordenes = DB::select("
		        	SELECT Date_format(o.created_at, '%m/%y') 
						AS 'fecha',
					SUM(TRUNCATE(( o.order_total ) - ( ( o.order_total ) * Round(( re.commission_percentage / 100 ), 2) ), 2))
						AS 'pago_restaurante',
					re.name AS 'restaurante'
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
			          	AND Date_format(o.created_at, '%m') = '".$mes."'
			          	AND Date_format(o.created_at, '%Y') = '".$anio."'
			          	AND o.restaurant_id = '".$rest."'
			        GROUP BY o.restaurant_id
			        ORDER BY o.order_id;
		        ");
				Log::info($mes.' '.$anio);
      		}

			#Generamos el csv
			$nombreArchivo = "ReporteIva_$fechaR.csv";
			$file = fopen("Reportes/".$nombreArchivo,"w");
			$end="
			";
			$sep=",";
			$tags = "";
			$data = "";

			#se genera tags
			$tags.="Fecha".$sep;
			$tags.="Restaurante".$sep;
			$tags.="Monto total pagado".$sep;
			$tags.="Iva a cobrar".$sep;			
			$tags.="Valor restaurante".$end;

			fwrite($file,$tags);

			foreach ($ordenes as $key => $row) {

				$totalIva = round($row->pago_restaurante * 0.13, 2);
				$totalSinIva = round($row->pago_restaurante / 1.13, 2);

				$data.= $row->fecha.$sep;
				$data.= $row->restaurante.$sep;
				$data.= $row->pago_restaurante.$sep;
				$data.= $totalIva.$sep;
				$data.= $totalSinIva.$end;

				fwrite($file,$data);
				$data="";
			}

			fclose($file);

			header("Content-disposition: attachment; filename=$nombreArchivo");
			header("Content-type: application/octet-stream");
			
			readfile("Reportes/".$nombreArchivo);	

			unlink("Reportes/".$nombreArchivo);
      	}
	}

	//Esta funcion será para mandar a la vista del reporte por restaurante, tipo pago y fechas
  	public function view_rest_pago(){

    	$user_id = Auth::id();    	
    	$res = Restaurant::get();
    	
  		return View::make('web.rest_pago_fechas')		
    		->with('res', $res);    	

  	}

  	public function reporte_Rest_Pago(){

  		if(Request::ajax()){			
			
			$fecha1 = Input::get('fecha_inicio');
	        $fecha2 = Input::get('fecha_fin');
	        $rest = Input::get('restaurante');
	        $pago = Input::get('pago');

	        $ordenes = DB::select("
	        	SELECT re.name AS 'restaurante',
					o.order_cod AS 'order_cod',
					m.payment_method as 'tipo_pago',
					re.name AS 'restaurante',
					( o.order_total + o.shipping_charge + o.credit_charge ) AS 'total',
					o.shipping_charge AS 'costo_envio',
					TRUNCATE(Round(( o.order_total ) * ( re.commission_percentage / 100 ), 2) , 2) AS 'comision_pf',
					TRUNCATE(( o.order_total ) - ( ( o.order_total ) * Round(( re.commission_percentage / 100 ), 2) ), 2)
						AS 'pago_restaurante',
					Date_format(o.created_at, '%d/%m/%y %r') 
       					AS 'fecha'
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
						AND o.restaurant_id = '".$rest."'
						AND  o.payment_method_id = ".$pago."
		          	AND o.created_at BETWEEN '".$fecha1."' AND '".$fecha2."'
		        
	        	ORDER BY o.order_id;
	        ");



	    	return Response::json(array('ordenes'=>$ordenes));
		}

  	}

}