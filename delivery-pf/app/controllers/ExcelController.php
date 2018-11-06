<?php

class ExcelController extends \BaseController {

	public function excelProductos(){
		$productos = DB::select("
        	SELECT 
        		r.restaurant_id AS 'cod_restaurante',
		       	r.NAME          AS 'restaurante',
		       	p.product_id    AS 'cod_producto',
		       	p.product       AS 'producto',
		       	p.section_id   as 'id_section',
		       	s.section	  as 'section',
			   	rt.tag_id	  AS 'id_tag',
			   	t.tag_name		AS 'tag'
			FROM 
				res_products p
		       	INNER JOIN res_sections s
	               	ON s.section_id = p.section_id
		       	INNER JOIN res_restaurants r
	               	ON r.restaurant_id = s.restaurant_id
				INNER JOIN res_tags as rt
					ON r.restaurant_id = rt.restaurant_id
				INNER JOIN com_tags as t
					ON rt.tag_id = t.tag_id
			where r.activate =1 and s.activate =1 and p.activate
			ORDER BY r.restaurant_id;
        ");
		
		#Generamos el csv
		$nombreArchivo = "Reporte_Productos.csv";
		$file = fopen("Reportes/".$nombreArchivo,"w");
		$end="
		";
		$sep=",";
		$tags = "";
		$data = "";

		#se genera tags
		$tags.="ID Restaurante".$sep;
		$tags.="Restaurante".$sep;
		$tags.="ID Producto".$sep;
		$tags.="Producto".$sep;
		$tags.="ID Categoria".$sep;
		$tags.="Categoria".$sep;
		$tags.="ID Tipo Comida".$sep;
		$tags.="Tipo Comida".$end;

		fwrite($file,$tags);

		foreach ($productos as $key => $row) {
			Log::info($key);
			$data.=$row->cod_restaurante.$sep;
			$data.=$row->restaurante.$sep;
			$data.=$row->cod_producto.$sep;
			$data.=$row->producto.$sep;
			$data.=$row->id_section.$sep;
			$data.=$row->section.$sep;
			$data.=$row->id_tag.$sep;
			$data.=$row->tag.$end;

			fwrite($file,$data);
			$data="";
		}

		fclose($file);

		header("Content-disposition: attachment; filename=$nombreArchivo");
		header("Content-type: application/octet-stream");
		
		readfile("Reportes/".$nombreArchivo);	

		unlink("Reportes/".$nombreArchivo);
	}

	public function excelRest_Pago_Fecha(){

		$fecha1 = Input::get('fecha1Exp');
        $fecha2 = Input::get('fecha2Exp');
        $rest = Input::get('restExp');
        $pago = Input::get('pagoExp');

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

        #Generamos el csv
		$nombreArchivo = "Reporte_Rest_Pago.csv";
		$file = fopen("Reportes/".$nombreArchivo,"w");
		$end="
		";
		$sep=",";
		$tags = "";
		//$data = "";

		#se genera tags
		$tags.="Fecha".$sep;
		$tags.="# Orden".$sep;
		$tags.="Restaurante".$sep;
		$tags.="Tipo Pago".$sep;
		$tags.="Monto Total".$sep;
		$tags.="Costo Envío".$sep;
		$tags.="Comisión Pidafacil".$sep;
		$tags.="Pago a Restaurante".$end;

		fwrite($file,$tags);

		foreach ($ordenes as $key => $row) {			
			$data=$row->fecha.$sep;
			$data.=$row->order_cod.$sep;
			$data.=$row->restaurante.$sep;
			$data.=$row->tipo_pago.$sep;
			$data.=$row->total.$sep;
			$data.=$row->costo_envio.$sep;
			$data.=$row->comision_pf.$sep;
			$data.=$row->pago_restaurante.$end;

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
