<?php

$gatewayURL = 'https://secure.networkmerchants.com/api/v2/three-step';
// Helper function to make building xml dom easier
function appendXmlNode($domDocument, $parentNode, $name, $value){
    $childNode      = $domDocument->createElement($name);
    $childNodeValue = $domDocument->createTextNode($value);
    $childNode->appendChild($childNodeValue);
    $parentNode->appendChild($childNode);
}

function sendXMLviaCurl($xmlRequest,$gatewayURL){
   //helper function demonstrating how to send the xml with curl
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

class PagoController extends \BaseController {

	public function createOrder(){

		$datos = $_POST['info'];
        Log::info($datos['datosCompra']);
		
		if(empty($_GET['token-id'])){//SI NO EXISTE EL TOKEN SE HACE EL STEP 1 Y 2
        	Log::info('ENTRO A TOKEN VACIO 1');
			if($datos['datosCompra']['payment_method_id'] == '1'){
	            //Si es pago efectivo se envía directo al controlador
				$action = 'store';
	        	return App::make('OrderController')->$action();
			}else if($datos['datosCompra']['payment_method_id'] == '3'){
	            //Si es pago tigo money se envía directo al controlador
	            $action = 'store';
	            return App::make('OrderController')->$action();
	        }else if($datos['datosCompra']['payment_method_id'] == '2'){
	            //Si es pago tarjeta se evalúa si existe el token-id para enviar los datos
	        	Log::info('ENTRO AL TIPO DE PAGO 2');
	        	$conf_amex = DB::table('conf_general_options')->get();
	        	//$typeT = Input::get('tipo_tarjeta');
	        	$typeT = 'visa';
	        	if($conf_amex[0]->encriptar_amex == 0 && $typeT == 'amex'){
	        		$action = 'store';
	            	return App::make('OrderController')->$action();	
	        	}else{	        		
	        		if(empty($_GET['token-id'])){
	        			Log::info('ENTRO A TOKEN VACIO');

		                $mesnew = '';
		                $anionew = '';

		                if($datos['datosCompra']['credit_expmonth'] < 10){
		                    $mesnew = "0".$datos['datosCompra']['credit_expmonth'];
		                }else{
		                    $mesnew = $datos['datosCompra']['credit_expmonth'];
		                }
		                
		                $anionew = substr($datos['datosCompra']['credit_expyear'], -2);

		                $ccexp = $mesnew.''.$anionew;// contruyendo mes/año con 2 digitos
		                $number_credit_card = $datos['datosCompra']['credit_card'];
		                $cvv = $datos['datosCompra']['secure_code'];

		                #if(Session::has('cart')){
		                    $cart = $datos['products'];
		                #}else{
		                #}

		                //Me traigo el costo de envío de la vista
		                $envio = 3;

		                //Evalúo el carrito para sumar el valor de todos los productos
		                if(is_array($cart)){
		                	Log::info('AQUI VA EL CART');
		                	#Log::info($cart);
		                	$totalF = 0;
		                    foreach($cart as $req_product){        
		                        $product = Product::findOrFail($req_product['product_id']);
	                            $nuevo_total = ($product->value * $req_product['quantity']);
	                            $totalF += $nuevo_total;
		                        /*if(is_array($req_order)){
		                            $totalF = 0;
		                            foreach ($req_order as $req_product) {
		                            	Log::info($req_product);
		                                $product = Product::findOrFail($req_product['product_id']);
		                                $nuevo_total = ($product->value * $req_product['quantity']);
		                                $totalF += $nuevo_total;
		                            }
		                        }*/
		                    }
		                }

		                //Obtengo el cargo por tarjeta
		                $cargo = round((($totalF + $envio) * 0.04),2);
		                //Obtengo el total a cobra
		                $monto = round(($totalF + $envio + $cargo),2);
		                Log::info('TOTAL: '.$totalF);
		                Log::info('ENVIO: '.$envio);
		                Log::info('CARGO: '.$cargo);
		                Log::info('Precio FINAL: '.$monto);
		                Session::put('monto', $datos);
		                return Redirect::route('paso1');
		                #$action = 'process_order';
	            		#return App::make('PagoController')->$action($formURL);
		            }else{
		            }		     
	        	}	        	
			}			
		}else{//SI EXISTE EL TOKEN SE HACE EL STEP 3
		}
		//return View::make('ionic.index');
	}

	public function process_order($tokenId){		
		//Log::info('SI AGARRA EL TOKEN '.$_GET['token-id']);
		Log::info(Route::getCurrentRoute()->getPath());
		//Log::info();
		Log::info('LLEGA A PROCESS ORDER');
		if(!empty($tokenId)){
			Log::info('SI EXISTE LA VARIABLE GET');
			Log::info($tokenId);

			$datos_bank = DB::table('bank_option')->get();
	        $key_= $datos_bank[1]->key_bank;
	        $url_bac = $datos_bank[1]->url_post_bank;
	        $key2 = '2F822Rw39fx762MaV7Yy86jXGTC7sCDy';
	        $gatewayURL = 'https://secure.networkmerchants.com/api/v2/three-step';
			                                                 
	    	// Step Three: Once the browser has been redirected, we can obtain the token-id and complete
		    // the transaction through another XML HTTPS POST including the token-id which abstracts the
		    // sensitive payment information that was previously collected by the Payment Gateway.
		    $token = explode("/", $formURL);
			Log::info('PARTE 6: '.$token[6]);

		    $xmlRequest = new DOMDocument('1.0','UTF-8');
		    $xmlRequest->formatOutput = true;
		    $xmlCompleteTransaction = $xmlRequest->createElement('complete-action');
		    appendXmlNode($xmlRequest, $xmlCompleteTransaction,'api-key',$key2);
		    appendXmlNode($xmlRequest, $xmlCompleteTransaction,'token-id',$token[6]);
		    $xmlRequest->appendChild($xmlCompleteTransaction);
		    Log::info('AQUI COMIENZA EL XML DEL PASO 3');
		    Log::info(print_r($xmlRequest, true));
		    // Process Step Three
		    $data = sendXMLviaCurl($xmlRequest,$gatewayURL);

		    $gwResponse = @new SimpleXMLElement((string)$data);
		    Log::info('RESULTADO FINAL ES: '.$gwResponse->{'result-code'});
		    Log::info('La sesion es: '.Session::get('service_type_id'));
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
						        <body style="background-image:url(http://localhost/pidafacil/public/images/background.jpg); background-repeat: no-repeat;">
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
						        <body style="background-image:url(http://localhost/pidafacil/public/images/background.jpg); background-repeat: no-repeat;">
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

					    /*-------------Envío automáticamente el formulario*/
					    print "<script type='text/javascript'>
					        	document.getElementById('createOrder').submit();
					     	</script>";
				        break;
				    case 200:
				        # Orden denegada
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");			        
				        break;
				    case 201:
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				        break;
				    case 202:
				        return Response::json("Lo sentimos, Fondos insuficientes. Intente con otro método de pago.");
				        break;
				    case 203:
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				        break;
				    case 204:
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				        break;
				    case 220:
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				        break;
				    case 221:
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				        break;    
				    case 222:
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				        break;            
				    case 223:
				        return Response::json("Lo sentimos, Su tarjeta esta expirada. Intente con otro método de pago.");
				        break;
				    case 224:
				        return Response::json("Fecha de vencimiento inválida, verifique sus datos");
				        break;
				    case 225:
				        return Response::json("Código de serguridad inválido, verifique sus datos");
				        break;  
				    case 240:
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				        break;
				    case 250:
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				    break;
				    case 251:
				    	$idusuario = Auth::id();
				        $user = User::find($idusuario);
				        $user->sospecha = 1; 
				        $user->save();
				        Log::info('Usuario a sospecha: '. $user->user_id );
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				        break; 
				    case 252:
				        $idusuario = Auth::id();
				        $user = User::find($idusuario);
				        $user->sospecha = 1; 
				        $user->save();
				        Log::info('Usuario a sospecha: '. $user->user_id );
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				        break;
				    case 253:
				        $idusuario = Auth::id();
				        $user = User::find($idusuario);
				        $user->sospecha = 1;
				        $user->save();
				        Log::info('Usuario a sospecha: '. $user->user_id );
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				        break; 
				    case 260:
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				        break;       
				    case 261:
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				        break; 
				    case 262:
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				        break;
				    case 263:
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				        break;
				    case 264:
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				        break;
				    case 300:
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				        break; 
				    case 400:
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				        break; 
				    case 410:
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				        break;  
				    case 411:
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				        break;
				    case 420:
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				        break;                               
				    case 421:
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				        break;                               
				    case 430:
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				        break;                               
				    case 440:
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				        break;                               
				    case 441:
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				        break;
				    case 460:
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				        break;
				    case 461:
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				        break;
				    
				    default:
				        # sino es ninguno de los parametros actuales de BAC
				        return Response::json("Error en la Transacción bancaria. Intente con otro método de pago.");
				        break;
				}
		    }elseif((string)$gwResponse->result == 2){
		    	Log::info('Resultado Error: '.$gwResponse->{'result-code'});
		    	Log::info('Resultado Auth: '.$gwResponse->{'authorization-code'});
		    	Log::info('Resultado text: '.$gwResponse->{'result-text'});

		        return Response::json("Lo sentimos, su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
		    }else{
		    	Log::info('Resultado 3: '.$gwResponse->{'result-code'});
		    	Log::info('Resultado 3: '.$gwResponse->{'result-text'});
		    	Log::info('Resultado 3: '.$gwResponse->{'authorization-code'});
		        return Response::json('En este momento su transacción no puede ser realizada por el Banco. Intente con otro método de pago');
		    }
		}else{
			#Log::info('NO LLEGÓ HASTA LA NUEVA FUNCIÓN*******');
		}
	}

	public function test(){
		Log::info('RUTA EN FUNCION TEST: '.Request::url());

		return View::make('ionic.index')->with('name', 'Steve');
		
	}

	public function step1(){
		$datos = Session::get('monto');
		#Log::info('LLEGO AL STEP1 FUNCION');
		if(isset($_GET['token-id'])){
			Log::info($_GET['token-id']);
		}
		#Log::info('RUTA 1: '.Request::url());
		#Log::info(print_r($datos, true));		
		$gatewayURL = 'https://secure.networkmerchants.com/api/v2/three-step';

		if(empty($_GET['token-id'])){
			$datos_bank = DB::table('bank_option')->get();
	        $key_= $datos_bank[1]->key_bank;
	        $url_bac = $datos_bank[1]->url_post_bank;
	        $key2 = '2F822Rw39fx762MaV7Yy86jXGTC7sCDy';

	        $mesnew = '';
            $anionew = '';

            if($datos['datosCompra']['credit_expmonth'] < 10){
                $mesnew = "0".$datos['datosCompra']['credit_expmonth'];
            }else{
                $mesnew = $datos['datosCompra']['credit_expmonth'];
            }
            
            $anionew = substr($datos['datosCompra']['credit_expyear'], -2);

            $ccexp = $mesnew.''.$anionew;// contruyendo mes/año con 2 digitos
            $number_credit_card = $datos['datosCompra']['credit_card'];
            $cvv = $datos['datosCompra']['secure_code'];

            #if(Session::has('cart')){
                $cart = $datos['products'];
            #}else{
            #}

            //Me traigo el costo de envío de la vista
            //Por el momento está quemado el costo de envío
            $envio = 3;

            //Evalúo el carrito para sumar el valor de todos los productos
            if(is_array($cart)){
            	$totalF = 0;
                foreach($cart as $req_product){        
                    $product = Product::findOrFail($req_product['product_id']);
                    $nuevo_total = ($product->value * $req_product['quantity']);
                    $totalF += $nuevo_total;
                    /*if(is_array($req_order)){
                        $totalF = 0;
                        foreach ($req_order as $req_product) {
                        	Log::info($req_product);
                            $product = Product::findOrFail($req_product['product_id']);
                            $nuevo_total = ($product->value * $req_product['quantity']);
                            $totalF += $nuevo_total;
                        }
                    }*/
                }
            }

            //Obtengo el cargo por tarjeta
            $cargo = round((($totalF + $envio) * 0.04),2);
            //Obtengo el total a cobra
            //$precio = round(($totalF + $envio + $cargo),2); 
            $precio = 1; 

			//Se envía el xml con los datos del step 1
			print ' <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
			print '
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
			appendXmlNode($xmlRequest, $xmlSale,'redirect-url', Request::url());
			appendXmlNode($xmlRequest, $xmlSale, 'amount', $precio);
			$xmlRequest->appendChild($xmlSale);
			
			#Log::info(print_r($xmlRequest, true));
			$data = sendXMLviaCurl($xmlRequest, $url_bac);

			// Parse Step One's XML response
			$gwResponse = @new SimpleXMLElement($data);
			Log::info('RESULTADO STEP 1');
			Log::info('result: '.$gwResponse->result);
			Log::info('result-text: '.$gwResponse->{'result-text'});
			Log::info('transaction: '.$gwResponse->{'transaction-id'});
			Log::info('result Code: '.$gwResponse->{'result-code'});
			Log::info('URL: '.$gwResponse->{'form-url'});

			//Se evalúa que el resultado del step 1 haya sido aprobado 
			if((string)$gwResponse->result == 1){
				#Log::info('ENTRO AL RESPONSE 1----------');
			    if($datos['datosCompra']['service_type_id'] == 1 || $datos['datosCompra']['service_type_id'] == 3){
			        //Datos en sesión para enviar a guardar la orden
			        Session::put('service_type_id', $datos['datosCompra']['service_type_id']);
			        Session::put('payment_method_id', $datos['datosCompra']['payment_method_id']);
			        Session::put('total_order', $totalF);
			        Session::put('cargo_tarjeta', $cargo);
			        Session::put('user_credit', $datos['datosCompra']['credit_name']);
			        Session::put('month', $mesnew);
			        Session::put('year', $anionew);
			        Session::put('envio', $envio);
			        Session::put('nombre_user', $datos['datosCompra']['customer']);
			        Session::put('telefono_user', $datos['datosCompra']['customer_phone']);
			        Session::put('address_id', $datos['datosCompra']['address_id']);
			        Session::put('secure_code', $datos['datosCompra']['secure_code']);
			        Session::put('user_id', $datos['datosCompra']['user_id']);
			        //Session::put('tipo_tarjeta', $datos['datosCompra']['tipo_tarjeta']);
			    }else{
			        Session::put('service_type_id', $datos['datosCompra']['service_type_id']);
			        Session::put('payment_method_id', $datos['datosCompra']['payment_method_id']);
			        Session::put('total_order', $totalF);
			        Session::put('cargo_tarjeta', $cargo);
			        Session::put('user_credit', $datos['datosCompra']['credit_name']);
			        Session::put('month', $mesnew);
			        Session::put('year', $anionew);
			        Session::put('nombre_user', $datos['datosCompra']['customer']);
			        Session::put('telefono_user', $datos['datosCompra']['customer_phone']);
			        //Session::put('address_id', $datos['datosCompra']['address_id']);
			        Session::put('secure_code', $datos['datosCompra']['secure_code']);
			        Session::put('hour', $datos['datosCompra']['pickup_hour']);
			        Session::put('minutes', $datos['datosCompra']['pickup_min']);
			        Session::put('restaurant_address', $datos['datosCompra']['res_address_id']);
			        Session::put('user_id', $datos['datosCompra']['user_id']);
			        //Session::put('tipo_tarjeta', $datos['datosCompra']['tipo_tarjeta']);
			    }
			    			    
			    //Si fué aprobado, se envía el step 2 con el resultado del 1
			    $formURL = $gwResponse->{'form-url'};			    
			    $datos = array(
				    "url" => $formURL,
				    "number_credit_card" => $number_credit_card,
				    "ccexp" => $ccexp,
				    "cvv" => $cvv
				);
			    #Log::info('RUTA 2: '.Request::url());
			    Log::info('LA URL DEL FORM ES *---*: '.$formURL);

			    //Envío a la vista donde se enviará el step 2
			    return View::make('ionic.index')->with(['url' => $formURL, 'number_credit_card' => $number_credit_card, 'ccexp' => $ccexp, 'cvv' => $cvv]);			    
			}else{
			    return "Error, received ".$data;
			}
	   	}else if(!empty($_GET['token-id'])){
			Log::info('SI EXISTE LA VARIABLE GET');
			$tokenId = $_GET['token-id'];
			Log::info($tokenId);
			Log::info('RUTA EN STEP 3: '.Request::url());
			$datos_bank = DB::table('bank_option')->get();
	        $key_= $datos_bank[1]->key_bank;
	        $url_bac = $datos_bank[1]->url_post_bank;
	        $key2 = '2F822Rw39fx762MaV7Yy86jXGTC7sCDy';
	        $gatewayURL = 'https://secure.networkmerchants.com/api/v2/three-step';
			                                                 
	    	// Step Three: Once the browser has been redirected, we can obtain the token-id and complete
		    // the transaction through another XML HTTPS POST including the token-id which abstracts the
		    // sensitive payment information that was previously collected by the Payment Gateway.
		    
		    $xmlRequest = new DOMDocument('1.0','UTF-8');
		    $xmlRequest->formatOutput = true;
		    $xmlCompleteTransaction = $xmlRequest->createElement('complete-action');
		    appendXmlNode($xmlRequest, $xmlCompleteTransaction,'api-key',$key_);
		    appendXmlNode($xmlRequest, $xmlCompleteTransaction,'token-id',$tokenId);
		    $xmlRequest->appendChild($xmlCompleteTransaction);
		    Log::info('AQUI COMIENZA EL XML DEL PASO 3');
		    #Log::info(print_r($xmlRequest, true));
		    // Process Step Three
		    $data = sendXMLviaCurl($xmlRequest,$gatewayURL);

		    $gwResponse = @new SimpleXMLElement((string)$data);
		    Log::info('RESULTADO FINAL ES: '.$gwResponse->{'result-code'});
		    Log::info('La sesion es: '.Session::get('service_type_id'));
		    if((string)$gwResponse->result == 1){
				Log::info('Resultado exito: '.$gwResponse->{'result-code'});
				Log::info('EL MENSAJE DEL RESULTADO ES: '.$gwResponse->{'result-text'});
				Log::info('TRANSACCION ID: '.$gwResponse->{'transaction-id'});
				echo "<script>window.close();</script>";
		    	switch ($gwResponse->{'result-code'}) {
				    case 100:
				    # Transaccion acceptada...

				    	/*if(Session::get('service_type_id') == 1 || Session::get('service_type_id') == 3){
				    		print ' <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
						    <html>
						        <head>
						            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
						        </head>
						        <body style="background-image:url(http://localhost/pidafacil/public/images/background.jpg); background-repeat: no-repeat;">
						            <form action="order/create2" method="POST" id="createOrder">
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
							            <input type ="hidden" name="user_id" value="'.Session::get('user_id').'">
							            <input type ="hidden" name="restaurant_id" value="56">
						            </form>
						        </body>
						    </html>';
				    	}else{
				    		print ' <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
						    <html>
						        <head>
						            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
						        </head>
						        <body style="background-image:url(http://localhost/pidafacil/public/images/background.jpg); background-repeat: no-repeat;">
						            <form action="order/create2" method="POST" id="createOrder">
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
							            <input type ="hidden" name="user_id" value="'.Session::get('user_id').'">
							            <input type ="hidden" name="restaurant_id" value="56">
						            </form>
						        </body>
						    </html>';
				    	}*/

					    /*-------------Envío automáticamente el formulario
					    print "<script type='text/javascript'>
					        	document.getElementById('createOrder').submit();
					     	</script>";*/
					    return Response::json("Transaccion correcta. Codigo Resultado: ". $gwResponse->{'result-code'}.'   '.$gwResponse->{'result-text'});
					    echo "<script>window.close();</script>";
				        break;
				    case 200:
				        #Orden denegada
				    	return Redirect::to($_SERVER['HTTP_REFERER']);
				        //return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago. Codigo Resultado: ". $gwResponse->{'result-code'}.'   '.$gwResponse->{'result-text'});				        
				        break;
				    case 201:
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro metodo de pago.". $gwResponse->{'result-code'});
				        echo "<script>window.close();</script>";
				        break;
				    case 202:
				        return Response::json("Lo sentimos, Fondos insuficientes. Intente con otro metodo de pago.". $gwResponse->{'result-code'});
				        break;
				    case 203:
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro metodo de pago.". $gwResponse->{'result-code'});
				        break;
				    case 204:
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro metodo de pago.". $gwResponse->{'result-code'});
				        break;
				    case 220:
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro metodo de pago.". $gwResponse->{'result-code'});
				        break;
				    case 221:
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro metodo de pago.". $gwResponse->{'result-code'});
				        break;    
				    case 222:
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro metodo de pago.". $gwResponse->{'result-code'});
				        break;            
				    case 223:
				        return Response::json("Lo sentimos, Su tarjeta esta expirada. Intente con otro metodo de pago.". $gwResponse->{'result-code'});
				        break;
				    case 224:
				        return Response::json("Fecha de vencimiento invalida, verifique sus datos");
				        break;
				    case 225:
				        return Response::json("Codigo de serguridad invalido, verifique sus datos");
				        break;  
				    case 240:
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro metodo de pago.");
				        break;
				    case 250:
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro metodo de pago.");
				    break;
				    case 251:
				    	$idusuario = Auth::id();
				        $user = User::find($idusuario);
				        $user->sospecha = 1; 
				        $user->save();
				        Log::info('Usuario a sospecha: '. $user->user_id );
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro metodo de pago.");
				        break; 
				    case 252:
				        $idusuario = Auth::id();
				        $user = User::find($idusuario);
				        $user->sospecha = 1; 
				        $user->save();
				        Log::info('Usuario a sospecha: '. $user->user_id );
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro metodo de pago.");
				        break;
				    case 253:
				        $idusuario = Auth::id();
				        $user = User::find($idusuario);
				        $user->sospecha = 1;
				        $user->save();
				        Log::info('Usuario a sospecha: '. $user->user_id );
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro metodo de pago.");
				        break; 
				    case 260:
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro metodo de pago.");
				        break;       
				    case 261:
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro metodo de pago.");
				        break; 
				    case 262:
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro metodo de pago.");
				        break;
				    case 263:
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro metodo de pago.");
				        break;
				    case 264:
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro metodo de pago.");
				        break;
				    case 300:
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro metodo de pago.");
				        break; 
				    case 400:
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro metodo de pago.");
				        break; 
				    case 410:
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro metodo de pago.");
				        break;  
				    case 411:
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro metodo de pago.");
				        break;
				    case 420:
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro metodo de pago.");
				        break;                               
				    case 421:
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro metodo de pago.");
				        break;                               
				    case 430:
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro metodo de pago.");
				        break;                               
				    case 440:
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro metodo de pago.");
				        break;                               
				    case 441:
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro metodo de pago.");
				        break;
				    case 460:
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro metodo de pago.");
				        break;
				    case 461:
				        return Response::json("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro metodo de pago.");
				        break;
				    
				    default:
				        # sino es ninguno de los parametros actuales de BAC
				        return Response::json(json_encode("Error en la Transaccion bancaria. Intente con otro metodo de pago."));
				        break;
				}
		    }elseif((string)$gwResponse->result == 2){
		    	Log::info('Resultado Error: '.$gwResponse->{'result-code'});
		    	Log::info('Resultado Auth: '.$gwResponse->{'authorization-code'});
		    	Log::info('Resultado text: '.$gwResponse->{'result-text'});		    	
		        return Response::json(json_encode("Lo sentimos, su tarjeta fue rechazada por el Banco. Intente con otro metodo de pago. Codigo Resultado: ". $gwResponse->{'result-code'}.'   texto: '.$gwResponse->{'result-text'}));
		    }else{
		    	Log::info('Resultado 3: '.$gwResponse->{'result-code'});
		    	Log::info('Resultado 3: '.$gwResponse->{'result-text'});
		    	Log::info('Resultado 3: '.$gwResponse->{'authorization-code'});
		        return Response::json(json_encode("En este momento su transaccion no puede ser realizada por el Banco. Intente con otro metodo de pago. Codigo:". $gwResponse->{'result-code'}.'   texto: '.$gwResponse->{'result-text'}));
		    }
		}else{			
		}
	}
}