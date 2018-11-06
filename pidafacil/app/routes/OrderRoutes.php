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

Route::group(array('prefix'=>'order','before'=>'auth'), function(){

	Route::get('/', array('uses' => 'OrderController@show'));

	#Route::post('/create', array('uses' => 'OrderController@store'));
	//Ruta para enviar parámetros en orden con tarjeta
	Route::post('/create', function(){
		
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


	                //AQUI EVALUAREMOS LOS BINS DE LAS TARJETAS*******************
	                //Obtengo la lista de bins
	                $listaBins = DB::table('list_bins')->select('num_bin')->get();
	                $arregloB = array();

	                //Guardo los bins en un nuevo arreglo
	                foreach ($listaBins as $key) {
	                	$arregloB[] = $key->num_bin;
	                }
	                
	                //Evaluaremos el número de tarjeta del cliente, obteniendo los primeros 6 digitos
	                $cardBin = substr($number_credit_card, 0, 6);
	                $config = DB::table('config_bins')->get();
					$porcentaje = $config[0]->porcentaje;
					$activate = $config[0]->activate;
					if ($activate == 1) {
						$descuentoMonto = round(($monto * $porcentaje/100),2);
					    $nuevoMonto = round(($monto - $descuentoMonto),2);

					}

	                /*****COMIENZA EVALUACION DEL BIN DE LA TARJETA PARA APLICAR DESCUENTO*****/
	                if (in_array($cardBin, $arregloB) && $activate == 1) {
					    Log::info("BIN DENTRO DE PROMO");
					    //Si existe promocion activa entonces hago descuento del monto total
				    	//Inserto en una tabla la transaccion
				    	$bin = new TransactionBin();
				    	$bin->num_tarjeta = substr($number_credit_card, strlen($number_credit_card)-4, strlen($number_credit_card));;
				    	$bin->num_bin = $cardBin;
				    	$bin->monto_total =$nuevoMonto;
				    	$bin->save();
				    	if (Session::has('bin_id'))
						{
						    Session::forget('bin_id');
						}
				    	Session::put('bin_id', $bin->id_transaction);

					}else{
						Log::info("No hay promoción activa con bins");
					    $nuevoMonto = $monto;
					} 

					/******TERMINA EVALUACION PARA DESCUENTO POR BIN*********/

					Log::info("EL NUEVO PRECIO FINAL ES: ".$nuevoMonto);

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
	                appendXmlNode($xmlRequest, $xmlSale, 'amount', $nuevoMonto);

	                $xmlRequest->appendChild($xmlSale);

	                $data = sendXMLviaCurl($xmlRequest, $url_bac);
	                Log::info('AQUI IMPRIMO DATA!!!!');
	                //Log::info(print_r($xmlRequest, true));
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
	                                <body style="background-image:url(http://pidafacil.com/images/background.jpg); background-repeat: no-repeat;">
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

	Route::get('/{restslug}', array('uses' => 'OrderController@lista' ));

	Route::get('/{restslug}/{id}', array('uses' => 'OrderController@orden' ));
/*
	// Addresses
	Route::get('/address', array('uses' => 'AddressController@index'));

	Route::post('/address/create', array('uses' => 'AddressController@store'));
*/
	Route::post('/edit', array('uses' => 'OrderController@update'));

    Route::post('/shipping_charge', array('uses' => 'OrderController@shipping_charge' ));

    Route::post('/getUserData', array('uses' => 'OrderController@getUserData' ));

    Route::post('/getTime', array('uses' => 'OrderController@getTimeEst'));
});