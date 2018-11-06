<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/
$gatewayURL = 'https://secure.networkmerchants.com/api/v2/three-step';
// Helper function to make building xml dom easier
function appendXmlNode($domDocument, $parentNode, $name, $value){
  	$childNode      = $domDocument->createElement($name);
  	$childNodeValue = $domDocument->createTextNode($value);
  	$childNode->appendChild($childNodeValue);
  	$parentNode->appendChild($childNode);
}

function sendXMLviaCurl($xmlRequest,$gatewayURL){
 	// helper function demonstrating how to send the xml with curl

  	$ch = curl_init(); // Initialize curl handle
  	curl_setopt($ch, CURLOPT_URL, $gatewayURL); // Set POST URL

  	$headers = array();
  	$headers[] = "Content-type: text/xml";
  	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // Add http headers to let it know we're sending XML
  	$xmlString = $xmlRequest->saveXML();
  	curl_setopt($ch, CURLOPT_FAILONERROR, 1); // Fail on errors
  	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // Allow redirects
  	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Return into a variable
  	curl_setopt($ch, CURLOPT_PORT, 443); // Set the port number
  	curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Times out after 30s
  	curl_setopt($ch, CURLOPT_POST, 1);
  	curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlString); // Add XML directly in POST

  	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

  	// This should be unset in production use. With it on, it forces the ssl cert to be valid
  	// before sending info.
  	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

  	if(!($data = curl_exec($ch))){
    	print  "curl error =>" .curl_error($ch) ."\n";
    	throw New Exception(" CURL ERROR :" . curl_error($ch));
  	}
  	curl_close($ch);

  	return $data;
}

//Ruta para raiz
Route::get('/', function(){
	return View::make('web.log_visor');
});

/****************Ruta para logueo*******************/
Route::post('doLogin', array('uses' => 'UserController@doLogin'));

Route::get('/logout', array('uses' => 'UserController@doLogout'));

/****************Fin Rutas logueo*******************/

//Ruta hacia el visor
Route::get('/delivery_pidafacil', array('as' => 'delivery_pidafacil', 'before' => 'auth', function(){
	ini_set('memory_limit', '-1');
	if(empty($_GET['token-id'])){
		//Si no existe, envío a controlador para traer los datos del pedido
		$action = 'deliveryPidafacil';
    	return App::make('RestaurantOrdersController')->$action();
	}else{
		Log::info('SI EXISTE LA VARIABLE GET');

      	$datos_bank = DB::table('bank_option')->get();
	    $key_= $datos_bank[1]->key_bank;
	    $url_bac = $datos_bank[1]->url_post_bank;
		$key2 = '2F822Rw39fx762MaV7Yy86jXGTC7sCDy';//Key de prueba
  		$gatewayURL = 'https://secure.networkmerchants.com/api/v2/three-step';
      	$order_id = Session::get('order_id');
      	$address_id = Session::get('address_id');
      	$user_id = Session::get('user_id');
	                                                     
    	// Step Three: Once the browser has been redirected, we can obtain the token-id and complete
	    // the transaction through another XML HTTPS POST including the token-id which abstracts the
	    // sensitive payment information that was previously collected by the Payment Gateway.
      	$tokenId = $_GET['token-id'];
      	$xmlRequest = new DOMDocument('1.0','UTF-8');
	  	$xmlRequest->formatOutput = true;
      	$xmlCompleteTransaction = $xmlRequest->createElement('complete-action');
      	appendXmlNode($xmlRequest, $xmlCompleteTransaction,'api-key',$key_);
      	appendXmlNode($xmlRequest, $xmlCompleteTransaction,'token-id',$tokenId);
      	$xmlRequest->appendChild($xmlCompleteTransaction);

      	// Process Step Three
      	$data = sendXMLviaCurl($xmlRequest,$gatewayURL);
      	$gwResponse = @new SimpleXMLElement((string)$data);
	    Log::info(print_r($gwResponse, true));
      	if((string)$gwResponse->result == 1){
        	Log::info('Resultado exito: '.$gwResponse->{'result-code'});
        	
        	switch ($gwResponse->{'result-code'}){
          		case 100:
	            	# Transaccion acceptada...
	            	$orden = Order::find($order_id);
	            	$orden->transaction_id = $gwResponse->{'transaction-id'};
	            	$orden->credit_card = 'Encriptada';
	            	$orden->save();

	            	Session::forget('order_id');

	            	Session::put('flash_message1', 'El cobro automático se realizó correctamente');
	            	return Redirect::to('http://ec2-54-236-21-142.compute-1.amazonaws.com/pidafacil-callcenter/public/delivery_pidafacil?fillter=1');
	            	break;
	          	case 200:
	            	# Orden denegada
	            	Session::put('flash_message1', 'El cobro automático se realizó correctamente');
	            	return Redirect::to('http://ec2-54-236-21-142.compute-1.amazonaws.com/pidafacil-callcenter/public/delivery_pidafacil?fillter=1');
	            	break;
	          	case 201:
	            	Session::put('flash_message2', 'No se pudo realizar el cobro automático.');
	            	return Redirect::to('http://ec2-54-236-21-142.compute-1.amazonaws.com/pidafacil-callcenter/public/delivery_pidafacil?fillter=1');
	            	break;
	          	case 202:
		            Session::put('flash_message2', 'Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.');
	            	return Redirect::to('http://ec2-54-236-21-142.compute-1.amazonaws.com/pidafacil-callcenter/public/delivery_pidafacil?fillter=1');
	            	break;
	          	case 203:
	            	Session::put('flash_message2', 'Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.');
	            	return Redirect::to('http://ec2-54-236-21-142.compute-1.amazonaws.com/pidafacil-callcenter/public/delivery_pidafacil?fillter=1');
	            	break;
	          	case 204:
	            	Session::put('flash_message2', 'Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.');
	            	return Redirect::to('http://ec2-54-236-21-142.compute-1.amazonaws.com/pidafacil-callcenter/public/delivery_pidafacil?fillter=1');
	            	break;
	          	case 220:
	            	Session::put('flash_message2', 'Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.');
	            	return Redirect::to('http://ec2-54-236-21-142.compute-1.amazonaws.com/pidafacil-callcenter/public/delivery_pidafacil?fillter=1');
	            	break;
	          	case 221:
		            Session::put('flash_message2', 'Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.');
		            return Redirect::to('http://ec2-54-236-21-142.compute-1.amazonaws.com/pidafacil-callcenter/public/delivery_pidafacil?fillter=1');
	    	        break;    
	        	case 222:
	            	Session::put('flash_message2', 'Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.');
	            	return Redirect::to('http://ec2-54-236-21-142.compute-1.amazonaws.com/pidafacil-callcenter/public/delivery_pidafacil?fillter=1');
	            	break;            
	          	case 223:
	            	Session::put('flash_message2', 'Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.');
	            	return Redirect::to('http://ec2-54-236-21-142.compute-1.amazonaws.com/pidafacil-callcenter/public/delivery_pidafacil?fillter=1');
	            	break;
	          	case 224:
	            	Session::put('flash_message2', 'Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.');
            		return Redirect::to('http://ec2-54-236-21-142.compute-1.amazonaws.com/pidafacil-callcenter/public/delivery_pidafacil?fillter=1');
	            	break;
	          	case 225:
	            	Session::put('flash_message2', 'Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.');
	            	return Redirect::to('http://ec2-54-236-21-142.compute-1.amazonaws.com/pidafacil-callcenter/public/delivery_pidafacil?fillter=1');
	            	break;  
	          	case 240:
	            	Session::put('flash_message2', 'Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.');
	            	return Redirect::to('http://ec2-54-236-21-142.compute-1.amazonaws.com/pidafacil-callcenter/public/delivery_pidafacil?fillter=1');
	            	break;
	          	case 250:
	            	Session::put('flash_message2', 'Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.');
	            	return Redirect::to('http://ec2-54-236-21-142.compute-1.amazonaws.com/pidafacil-callcenter/public/delivery_pidafacil?fillter=1');
	            	break;
	          	case 251:
	            	$idusuario = $user_id;
	            	$user = User::find($idusuario);
		            $user->sospecha = 1; 
		            $user->save();
		            Log::info('Usuario a sospecha: '. $user->user_id );
		            Session::put('flash_message2', 'Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.');
	            	return Redirect::to('http://ec2-54-236-21-142.compute-1.amazonaws.com/pidafacil-callcenter/public/delivery_pidafacil?fillter=1');
	            	break; 
	          	case 252:
		            $idusuario = $user_id;
		            $user = User::find($idusuario);
		            $user->sospecha = 1; 
		            $user->save();
		            Log::info('Usuario a sospecha: '. $user->user_id );
		            Session::put('flash_message2', 'Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.');
		            return Redirect::to('http://ec2-54-236-21-142.compute-1.amazonaws.com/pidafacil-callcenter/public/delivery_pidafacil?fillter=1');
		            break;
	          	case 253:
		            $idusuario = $user_id;
		            $user = User::find($idusuario);
		            $user->sospecha = 1; 
		            $user->save();
		            Log::info('Usuario a sospecha: '. $user->user_id );
		            Session::put('flash_message2', 'Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.');
		            return Redirect::to('http://ec2-54-236-21-142.compute-1.amazonaws.com/pidafacil-callcenter/public/delivery_pidafacil?fillter=1');
		            break; 
	          	case 260:
		            Session::put('flash_message2', 'Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.');
		            return Redirect::to('http://ec2-54-236-21-142.compute-1.amazonaws.com/pidafacil-callcenter/public/delivery_pidafacil?fillter=1');
		            break;       
	          	case 261:
		            Session::put('flash_message2', 'Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.');
		            return Redirect::to('http://ec2-54-236-21-142.compute-1.amazonaws.com/pidafacil-callcenter/public/delivery_pidafacil?fillter=1');
		            break; 
	          	case 262:
		            Session::put('flash_message2', 'Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.');
		            return Redirect::to('http://ec2-54-236-21-142.compute-1.amazonaws.com/pidafacil-callcenter/public/delivery_pidafacil?fillter=1');
		            break;
	          	case 263:
		            Session::put('flash_message2', 'Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.');
		            return Redirect::to('http://ec2-54-236-21-142.compute-1.amazonaws.com/pidafacil-callcenter/public/delivery_pidafacil?fillter=1');
		            break;
	          	case 264:
		            Session::put('flash_message2', 'Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.');
		            return Redirect::to('http://ec2-54-236-21-142.compute-1.amazonaws.com/pidafacil-callcenter/public/delivery_pidafacil?fillter=1');
		            break;
	          	case 300:
		            Session::put('flash_message2', 'Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.');
		            return Redirect::to('http://ec2-54-236-21-142.compute-1.amazonaws.com/pidafacil-callcenter/public/delivery_pidafacil?fillter=1');
		            break; 
	          	case 400:
		            Session::put('flash_message2', 'Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.');
		            return Redirect::to('http://ec2-54-236-21-142.compute-1.amazonaws.com/pidafacil-callcenter/public/delivery_pidafacil?fillter=1');
		            break; 
	          	case 410:
		            Session::put('flash_message2', 'Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.');
		            return Redirect::to('http://ec2-54-236-21-142.compute-1.amazonaws.com/pidafacil-callcenter/public/delivery_pidafacil?fillter=1');
		            break;  
	          	case 411:
		            Session::put('flash_message2', 'Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.');
		            return Redirect::to('http://ec2-54-236-21-142.compute-1.amazonaws.com/pidafacil-callcenter/public/delivery_pidafacil?fillter=1');
		            break;
	          	case 420:
		            Session::put('flash_message2', 'Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.');
		            return Redirect::to('http://ec2-54-236-21-142.compute-1.amazonaws.com/pidafacil-callcenter/public/delivery_pidafacil?fillter=1');
		            break;                               
	          	case 421:
		            Session::put('flash_message2', 'Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.');
		            return Redirect::to('http://ec2-54-236-21-142.compute-1.amazonaws.com/pidafacil-callcenter/public/delivery_pidafacil?fillter=1');
		            break;                               
	          	case 430:
		            Session::put('flash_message2', 'Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.');
		            return Redirect::to('http://ec2-54-236-21-142.compute-1.amazonaws.com/pidafacil-callcenter/public/delivery_pidafacil?fillter=1');
		            break;                               
	          	case 440:
		            Session::put('flash_message2', 'Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.');
		            return Redirect::to('http://ec2-54-236-21-142.compute-1.amazonaws.com/pidafacil-callcenter/public/delivery_pidafacil?fillter=1');
		            break;                               
	          	case 441:
		            Session::put('flash_message2', 'Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.');
		            return Redirect::to('http://ec2-54-236-21-142.compute-1.amazonaws.com/pidafacil-callcenter/public/delivery_pidafacil?fillter=1');
		            break;                               
	          	case 460:
		            Session::put('flash_message2', 'Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.');
		            return Redirect::to('http://ec2-54-236-21-142.compute-1.amazonaws.com/pidafacil-callcenter/public/delivery_pidafacil?fillter=1');
		            break;  
	          	case 461:
		            Session::put('flash_message2', 'Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.');
		            return Redirect::to('http://ec2-54-236-21-142.compute-1.amazonaws.com/pidafacil-callcenter/public/delivery_pidafacil?fillter=1');
		            break;                                           
	          	default:
	            # sino es ninguno de los parametros actuales de BAC
	            Session::put('flash_message2', 'Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.');
	            return Redirect::to('http://ec2-54-236-21-142.compute-1.amazonaws.com/pidafacil-callcenter/public/delivery_pidafacil?fillter=1');
	            break;
	        }
      	}elseif((string)$gwResponse->result == 2){
        	Log::info('Resultado Error: '.$gwResponse->{'result-code'});
	        Log::info('Resultado Auth: '.$gwResponse->{'authorization-code'});
	        Log::info('Resultado text: '.$gwResponse->{'result-text'});

	        Session::put('flash_message2', 'Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.');
	            return Redirect::to('http://ec2-54-236-21-142.compute-1.amazonaws.com/pidafacil-callcenter/public/delivery_pidafacil?fillter=1');
      	}else{
	        Log::info('Resultado Error: '.$gwResponse->{'result-code'});
	        Log::info('Resultado Auth: '.$gwResponse->{'authorization-code'});
	        Log::info('Resultado text: '.$gwResponse->{'result-text'});
	        Session::put('flash_message2', 'Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.');
        	return Redirect::to('http://ec2-54-236-21-142.compute-1.amazonaws.com/pidafacil-callcenter/public/delivery_pidafacil?fillter=1');
    	}
	}
}));

//Ruta para aceptar un pedido
Route::post('forward', array('uses' => 'RestaurantOrdersController@forward'));

Route::post('backward/{id}', array('uses' => 'RestaurantOrdersController@backward'));

//Ruta para cambiar de estado, a cancelado, rechasada e incobrable
Route::post('cancel', array('uses' => 'RestaurantOrdersController@cancel'));

//Ruta para cambiar a estado orden sin motorista asignado
Route::post('ord_sm', array('uses' => 'RestaurantOrdersController@sinMotorista'));

//Enviar correos informando el cambio de estado
Route::Post('cambioEstado', array('uses' => 'EmailController@cambioEstado'));

Route::post('baddebt/{id}', array('uses' => 'RestaurantOrdersController@baddebt'));

Route::post('reject/{id}', array('uses' => 'RestaurantOrdersController@reject'));

Route::post('accept', array('uses' => 'RestaurantOrdersController@accept'));

//Actualizar restaurante(sucrusal) de la orden
Route::post('asignar', array('uses' => 'RestaurantOrdersController@asignar'));

//Tomar el tiempo
Route::post('time', array('uses' => 'RestaurantOrdersController@time'));

//Activa y desactiva sonido de notificacion
Route::post('sond_alert', array('uses' => 'RestaurantOrdersController@sond_alert'));

//Ruta para agregar observación a la orden
Route::post('comment', array('uses' => 'RestaurantOrdersController@addComment'));
Route::post('total_obs', array('uses' => 'RestaurantOrdersController@countObservacion'));
Route::post('observaciones', array('uses' => 'RestaurantOrdersController@allObservaciones'));

//Rutas para notificaciones en el visor
Route::post('new_ord', array('uses' => 'RestaurantOrdersController@new_orders'));
Route::post('new_ord_cc', array('uses' => 'RestaurantOrdersController@new_orders_cc'));
Route::post('new_ord_rest', array('uses' => 'RestaurantOrdersController@new_orders_rest'));

//Ruta para editar una orden específica
Route::get('edit_orden/{id}', array('uses' => 'RestaurantOrdersController@editOrder'));
Route::post('editar_pedido', array('uses' => 'RestaurantOrdersController@actualizar_orden'));

//Ruta para verificar los tiempos de las ordenes asignadas
Route::post('total_tiempo', array('uses' => 'RestaurantOrdersController@time_order'));

//Ruta para enviar a expirada una orden
Route::post('expirar_orden', array('uses' => 'RestaurantOrdersController@expiracion'));

//Ruta para mostrar el mapa de google
Route::get('mapaG', array('as' => 'mapaG', 'uses' => 'UserController@MostrarMapa'));

//Ruta para obtener las coordenadas de los motociclistas
Route::post('coords_moto', array('uses' => 'HomeController@coordenadasMoto'));

//Ruta para mostrar pago a motociclistas
Route::get('pagoM', array('as' => 'pagoM', 'uses' => 'RestaurantOrdersController@pagoMoto'));

//Ruta para busqueda de ordenes para pago motociclistas
Route::post('busquedaMoto', array('uses' => 'RestaurantOrdersController@ordenesBusqueda'));

//Ruta para vista de mantenimiento de motociclistas
Route::get('manteMoto', array('uses' => 'MotoController@newVista'));

//Ruta para ingresar nuevos motociclista
Route::post('newMoto', array('uses' => 'MotoController@addMoto'));

//Ruta para la vista de todos los motociclistas creados
Route::get('listMoto', array('as' => 'listMoto', 'uses' => 'MotoController@allMotos'));

//Ruta para obtener los datos de motociclistas para editar
Route::post('datosMot', array('uses' => 'MotoController@obtenerDatos'));

//Ruta para editar datos de motociclistas
Route::post('editarMot', array('uses' => 'MotoController@editarMoto'));

//Ruta para eliminar motociclistas
Route::post('deleteMoto', array('uses' => 'MotoController@eliminarMoto'));

//Ruta para el pago automático desde callcenter
Route::post('pagoAuto', array('uses' => 'RestaurantOrdersController@cobroAuto'));

/*******RUTAS PARA CREAR UNA ORDEN POR CALLCENTER******/
Route::get('listRest', array('as' => 'listRest', 'uses' => 'OrderController@allRest'));

//Ruta para obtener el id del restaurante y mostrar los productos del mismo
Route::get('productRest/{id_rest}', array('as' => 'productos', 'uses' => 'OrderController@allProducts'));

//Ruta para obtener las condiciones e ingredientes de cada producto
Route::post('detalleProd', array('uses' => 'OrderController@dataProduct'));

//Ruta para agregar productos al carrito
Route::post('addCart', array('uses' => 'OrderController@add'));

Route::post('searchProd', array('uses' => 'OrderController@searchProduct'));

Route::get('/tags-search/{tag}', array('uses' => 'SearchController@tags'));

Route::get('cart', array('as' => 'cart', 'uses' => 'OrderController@viewCart'));

//Ruta para enviar a checkout
//Route::get('checkout', array('uses' => 'OrderController@checkout'));
Route::get('/checkout', array(function(){

	//Evalúo si existe la variable get con el token del step 2
	if(empty($_GET['token-id'])){
		//Si no existe, envío a controlador para traer los datos del pedido
		$action = 'checkout';
    	return App::make('OrderController')->$action();
	}else{
		Log::info('SI EXISTE LA VARIABLE GET');

		$datos_bank = DB::table('bank_option')->get();
        $key_= $datos_bank[1]->key_bank;
        $url_bac = $datos_bank[1]->url_post_bank;
        $key2 = '2F822Rw39fx762MaV7Yy86jXGTC7sCDy';//Key de prueba
        $gatewayURL = 'https://secure.networkmerchants.com/api/v2/three-step';
		                                                 
    	// Step Three: Once the browser has been redirected, we can obtain the token-id and complete
	    // the transaction through another XML HTTPS POST including the token-id which abstracts the
	    // sensitive payment information that was previously collected by the Payment Gateway.
	    $tokenId = $_GET['token-id'];
	    $xmlRequest = new DOMDocument('1.0','UTF-8');
	    $xmlRequest->formatOutput = true;
	    $xmlCompleteTransaction = $xmlRequest->createElement('complete-action');
	    appendXmlNode($xmlRequest, $xmlCompleteTransaction,'api-key',$key_);
	    appendXmlNode($xmlRequest, $xmlCompleteTransaction,'token-id',$tokenId);
	    $xmlRequest->appendChild($xmlCompleteTransaction);

	    // Process Step Three
	    $data = sendXMLviaCurl($xmlRequest,$gatewayURL);

	    $gwResponse = @new SimpleXMLElement((string)$data);
	    
	    if((string)$gwResponse->result == 1){
			Log::info('Resultado exito: '.$gwResponse->{'result-code'});

	    	switch ($gwResponse->{'result-code'}) {
			    case 100:
			    # Transaccion acceptada...
			    	if(Session::get('service_type_id') == 1 || Session::get('service_type_id') == 3){
			    		print ' <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
					    <html>
					        <head>
					            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
					        </head>
					        <body style="background-image:url(http://ec2-54-236-21-142.compute-1.amazonaws.com/pidafacil-callcenter/public/images/background.jpg); background-repeat: no-repeat;">
					            <form action="create2" method="POST" id="createOrder">
						            <input type ="hidden" name="service_type_id" value="'.Session::get('service_type_id').'">
						            <input type ="hidden" name="payment_method_id" value="'.Session::get('payment_method_id').'">
						            <input type ="hidden" name="user_credit" value="'.Session::get('user_credit').'">
						            <input type ="hidden" name="month" value="'.Session::get('month').'">
						            <input type ="hidden" name="year" value="'.Session::get('year').'">
						            <input type ="hidden" name="nombre_user" value="'.Session::get('nombre_user').'">
						            <input type ="hidden" name="telefono_user" value="'.Session::get('telefono_user').'">
						            <input type ="hidden" name="address_id" value="'.Session::get('address_id').'">
						            <input type ="hidden" name="secure_code" value="'.Session::get('secure_code').'">
						            <input type ="hidden" name="credit_card" value="Encriptada">
						            <input type ="hidden" name="transaction_id" value="'.$gwResponse->{'transaction-id'}.'">
					            </form>
					        </body>
					    </html>';
			    	}else{
			    		print ' <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
					    <html>
					        <head>
					            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
					        </head>
					        <body style="background-image:url(http://ec2-54-236-21-142.compute-1.amazonaws.com/pidafacil-callcenter/public/images/background.jpg); background-repeat: no-repeat;">
					            <form action="create2" method="POST" id="createOrder">
						            <input type ="hidden" name="service_type_id" value="'.Session::get('service_type_id').'">
						            <input type ="hidden" name="payment_method_id" value="'.Session::get('payment_method_id').'">
						            <input type ="hidden" name="user_credit" value="'.Session::get('user_credit').'">
						            <input type ="hidden" name="month" value="'.Session::get('month').'">
						            <input type ="hidden" name="year" value="'.Session::get('year').'">
						            <input type ="hidden" name="nombre_user" value="'.Session::get('nombre_user').'">
						            <input type ="hidden" name="telefono_user" value="'.Session::get('telefono_user').'">
						            <input type ="hidden" name="secure_code" value="'.Session::get('secure_code').'">
						            <input type ="hidden" name="credit_card" value="Encriptada">
						            <input type ="hidden" name="transaction_id" value="'.$gwResponse->{'transaction-id'}.'">
						            <input type ="hidden" name="hour" value="'.Session::get('hour').'">
						            <input type ="hidden" name="minutes" value="'.Session::get('minutes').'">
						            <input type ="hidden" name="restaurant_address" value="'.Session::get('restaurant_address').'">
					            </form>
					        </body>
					    </html>';
			    	}

				    /*Envío automáticamente el formulario*/
				    echo "<script type='text/javascript'>
				        	document.getElementById('createOrder').submit();
				     	</script>";
			        break;
			    case 200:
			        # Orden denegada
			        return Redirect::to('/checkout')
                        ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
			        break;
			    case 201:
			        return Redirect::to('/checkout')
                        ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
			        break;
			    case 202:
			        return Redirect::to('/checkout')
                        ->withErrors("Lo sentimos, Fondos insuficientes. Intente con otro método de pago.");
			        break;
			    case 203:
			        return Redirect::to('/checkout')
                        ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
			        break;
			    case 204:
			        return Redirect::to('/checkout')
                        ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
			        break;
			    case 220:
			        return Redirect::to('/checkout')
                        ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
			        break;
			    case 221:
			        return Redirect::to('/checkout')
                        ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
			        break;    
			    case 222:
			        return Redirect::to('/checkout')
                        ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
			        break;            
			    case 223:
			        return Redirect::to('/checkout')
                        ->withErrors("Lo sentimos, Su tarjeta esta expirada. Intente con otro método de pago.");
			        break;
			    case 224:
			        return Redirect::to('/checkout')
                        ->withErrors("Fecha de vencimiento inválida, verifique sus datos");
			        break;
			    case 225:
			        return Redirect::to('/checkout')
                        ->withErrors("Código de serguridad inválido, verifique sus datos");
			        break;  
			    case 240:
			        return Redirect::to('/checkout')
                        ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
			        break;
			    case 250:
			        return Redirect::to('/checkout')
                        ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
			    	break;
			    case 251:
			    	$idusuario = Auth::id();
			        $user = User::find($idusuario);
			        $user->sospecha = 1; 
			        $user->save();
			        Log::info('Usuario a sospecha: '. $user->user_id );
			        return Redirect::to('/checkout')
                        ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
			        break; 
			    case 252:
			        $idusuario = Auth::id();
			        $user = User::find($idusuario);
			        $user->sospecha = 1; 
			        $user->save();
			        Log::info('Usuario a sospecha: '. $user->user_id );
			        return Redirect::to('/checkout')
                        ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
			        break;
			    case 253:
			        $idusuario = Auth::id();
			        $user = User::find($idusuario);
			        $user->sospecha = 1;
			        $user->save();
			        Log::info('Usuario a sospecha: '. $user->user_id );
			        return Redirect::to('/checkout')
                        ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
			        break; 
			    case 260:
			        return Redirect::to('/checkout')
                        ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
			        break;       
			    case 261:
			        return Redirect::to('/checkout')
                    	->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
			        break; 
			    case 262:
			        return Redirect::to('/checkout')
                        ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
			        break;
			    case 263:
			        return Redirect::to('/checkout')
                        ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
			        break;
			    case 264:
			        return Redirect::to('/checkout')
                        ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
			        break;
			    case 300:
			        return Redirect::to('/checkout')
                        ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
			        break; 
			    case 400:
			        return Redirect::to('/checkout')
                        ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
			        break; 
			    case 410:
			        return Redirect::to('/checkout')
                        ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
			        break;  
			    case 411:
			        return Redirect::to('/checkout')
                        ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
			        break;
			    case 420:
			        return Redirect::to('/checkout')
                        ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
			        break;                               
			    case 421:
			        return Redirect::to('/checkout')
                        ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
			        break;                               
			    case 430:
			        return Redirect::to('/checkout')
                        ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
			        break;                               
			    case 440:
			        return Redirect::to('/checkout')
                        ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
			        break;                               
			    case 441:
			        return Redirect::to('/checkout')
                        ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
			        break;                               
			    case 460:
			        return Redirect::to('/checkout')
                        ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
			        break;  
			    case 461:
			        return Redirect::to('/checkout')
                        ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
			        break;                                 
			    
			    default:
			        # sino es ninguno de los parametros actuales de BAC
			        return Redirect::to('/checkout')
                        ->withErrors("Error en la Transacción bancaria. Intente con otro método de pago.");
			        break;
			}
	    }elseif((string)$gwResponse->result == 2){
	    	Log::info('Resultado Error: '.$gwResponse->{'result-code'});
	    	Log::info('Resultado Auth: '.$gwResponse->{'authorization-code'});
	    	Log::info('Resultado text: '.$gwResponse->{'result-text'});

	        return Redirect::to('/checkout')
                ->withErrors("Lo sentimos, su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
	    }else{
	    	Log::info('Resultado 3: '.$gwResponse->{'result-code'});
	        return Redirect::to('/checkout')
                ->withErrors('En este momento su transacción no puede ser realizada por el Banco. Intente con otro método de pago');
	    }
	}
}));

//Rutas para cargar información en vista checkout
Route::post('/shipping_charge', array('uses' => 'OrderController@shipping_charge'));

Route::post('/getUserData', array('uses' => 'OrderController@getUserData'));

Route::post('/getTime', array('uses' => 'OrderController@getTimeEst'));

Route::post('getDataOrder', array('uses' => 'OrderController@getOrderData'));

//Agregar nuevo usuario
Route::post('/register', array('uses' => 'UserController@store'));

//Nueva dirección
Route::post('/address/create', array('uses' => 'AddressController@create'));

//CREACION DE ORDEN
Route::post('/order/create', function(){
		
	if(Input::get('payment_method_id') == '1'){
        //Si es pago efectivo se envía directo al controlador
		$action = 'store';
    	return App::make('OrderController')->$action();
	}else if(Input::get('payment_method_id') == '3'){
        //Si es pago tigo money se envía directo al controlador
        $action = 'store';
        return App::make('OrderController')->$action();
    }else if(Input::get('payment_method_id') == '2'){
        //Si es pago tarjeta se evalúa si existe el token-id para enviar los datos
        //Si es tarjeta AMEX se envía
    	$conf_amex = DB::table('conf_general_options')->get();
    	$typeT = Input::get('tipo_tarjeta');
    	if($conf_amex[0]->encriptar_amex == 0 && $typeT == 'amex'){
    		$action = 'store';
        	return App::make('OrderController')->$action();	
    	}else{
    		if(empty($_GET['token-id'])){
                $mesnew = '';
                $anionew = '';

                if(Input::get('month') < 10){
                    $mesnew = "0".Input::get('month');
                }else{
                    $mesnew = Input::get('month');
                }
                
                $anionew = substr(Input::get('year'), -2);

                $ccexp = $mesnew.''.$anionew;// contruyendo mes/año con 2 digitos
                $number_credit_card = Input::get('credit_card');
                $cvv = Input::get('secure_code');

                //OBTENGO LOS DATOS DEL BANCO DESDE BDD
                $datos_bank = DB::table('bank_option')->get();
                $key_= $datos_bank[1]->key_bank;
                $url_bac = $datos_bank[1]->url_post_bank;
                $key2 = '2F822Rw39fx762MaV7Yy86jXGTC7sCDy';//Key de prueba

                if(Session::has('cart')){
                    $cart = Session::get('cart');
                }else{
                }

                //Me traigo el costo de envío de la vista
                $envio = Input::get('costo_envio');

                //Evalúo el carrito para sumar el valor de todos los productos
                if(is_array($cart)){
                    foreach($cart as $req_restaurant => $req_order){        
                        if(is_array($req_order)){
                            $totalF = 0;
                            foreach ($req_order as $key => $req_product) {
                                $product = Product::findOrFail($req_product['product_id']);
                                $nuevo_total = ($product->value * $req_product['quantity']);
                                $totalF += $nuevo_total;
                            }
                        }
                    }
                }

                //Obtengo el cargo por tarjeta
                $cargo = round((($totalF + $envio) * 0.04),2);
                
                //Obtengo el total a cobrar
                $monto = round(($totalF + $envio + $cargo),2);
                Log::info('TOTAL: '.$totalF);
                Log::info('ENVIO: '.$envio);
                Log::info('CARGO: '.$cargo);
                Log::info('Precio FINAL: '.$monto);

                //Se envía el xml con los datos del step 1**************************
                echo ' <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
                echo '
                    <html>
                        <head>
                            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                        </head>
                        <body>
                        </body>
                    </html>';

                $xmlRequest = new DOMDocument('1.0','UTF-8');
                $xmlRequest->formatOutput = true;
                $xmlSale = $xmlRequest->createElement('sale');

                appendXmlNode($xmlRequest, $xmlSale,'api-key',$key_);
                appendXmlNode($xmlRequest, $xmlSale,'redirect-url',$_SERVER['HTTP_REFERER']);
                appendXmlNode($xmlRequest, $xmlSale, 'amount', $monto);

                $xmlRequest->appendChild($xmlSale);

                $data = sendXMLviaCurl($xmlRequest, $url_bac);
                Log::info('AQUI IMPRIMO DATA!!!!');
                Log::info(print_r($xmlRequest, true));
                //Log::info($xmlRequest);
                // Parse Step One's XML response
                $gwResponse = @new SimpleXMLElement($data);

                Log::info('result: '.$gwResponse->result);
                Log::info('result-text: '.$gwResponse->{'result-text'});
                Log::info('transaction: '.$gwResponse->{'transaction-id'});
                Log::info('result Code: '.$gwResponse->{'result-code'});
                Log::info('URL: '.$gwResponse->{'form-url'});

                //Se evalúa que el resultado del step 1 haya sido aprobado 
                if((string)$gwResponse->result == 1){

                	//Evaluo si es orden a domicilio propio o pidafacil
                    if(Input::get('service_type_id') == 1 || Input::get('service_type_id') == 3){	                        
                        //PONGO LOS DATOS EN SESION PARA OBTENERLOS
                        //Datos en sesión para enviar a guardar la orden
                        Session::put('service_type_id', Input::get('service_type_id'));
                        Session::put('payment_method_id', Input::get('payment_method_id'));
                        Session::put('total_order', $totalF);
                        Session::put('cargo_tarjeta', $cargo);
                        Session::put('user_credit', Input::get('user_credit'));
                        Session::put('month', $mesnew);
                        Session::put('year', $anionew);
                        Session::put('envio', $envio);
                        Session::put('nombre_user', Input::get('nombre_user'));
                        Session::put('telefono_user', Input::get('telefono_user'));
                        Session::put('address_id', Input::get('address_id'));
                        Session::put('secure_code',Input::get('secure_code'));
                        Session::put('tipo_tarjeta',Input::get('tipo_tarjeta'));
                    }else{//Orden para llevar
                        Session::put('service_type_id', Input::get('service_type_id'));
                        Session::put('payment_method_id', Input::get('payment_method_id'));
                        Session::put('total_order', $totalF);
                        Session::put('cargo_tarjeta', $cargo);
                        Session::put('user_credit', Input::get('user_credit'));
                        Session::put('month', $mesnew);
                        Session::put('year', $anionew);
                        Session::put('nombre_user', Input::get('nombre_user'));
                        Session::put('telefono_user', Input::get('telefono_user'));
                        Session::put('address_id', Input::get('address_id'));
                        Session::put('secure_code',Input::get('secure_code'));
                        Session::put('hour', Input::get('hour'));
                        Session::put('minutes', Input::get('minutes'));
                        Session::put('restaurant_address', Input::get('restaurant_address'));
                        Session::put('tipo_tarjeta',Input::get('tipo_tarjeta'));
                    }

                    //Si fue aprobado, se envía el step 2 con el resultado del 1
                    //El cual me devolverá el token_id
                    $formURL = $gwResponse->{'form-url'};
                    
                    print ' <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                            <html>
                            <head>
                                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                            </head>
                                <body style="background-image:url(http://ec2-54-236-21-142.compute-1.amazonaws.com/pidafacil-callcenter/public/images/background.jpg); background-repeat: no-repeat;">
                                    <form action="'.$formURL. '" method="POST" id="formStep2">
                                        <table>
                                            <tr><td><INPUT type ="hidden" name="billing-cc-number" value="'.$number_credit_card.'"> </td></tr>
                                            <tr><td><INPUT type ="hidden" name="billing-cc-exp" value="'.$ccexp.'"> </td></tr>
                                            <tr><td><INPUT type ="hidden" name="cvv" value="'.$cvv.'"></td></tr>
                                        </table>
                                    </form>
                                </body>
                            </html>';
                    
                    /*Envío automáticamente el formulario*/
                    echo "<script type='text/javascript'>
                            document.getElementById('formStep2').submit();
                     	</script>";
                }else{
                    return "Error, received ".$data;
                }
            }else{
            }
    	}
	}
});

//Ruta para eliminar el carrito de compras
Route::post('cart/destroy', array('uses' => 'OrderController@destroy'));

//Ruta para eliminar un producto del carrito
Route::post('cart/delete', array('uses' => 'OrderController@delete'));

Route::post('/create2', array('uses' => 'OrderController@store'));

// Luhn algorithm
Validator::resolver(function($translator, $data, $rules, $messages)  {
    return new ArdaValidator($translator, $data, $rules, $messages);
});