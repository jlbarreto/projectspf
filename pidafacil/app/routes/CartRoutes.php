<?php

Route::group(array('prefix'=>'cart'), function(){

	Route::get('/', array('uses' => 'CartController@index'));

	Route::post('/add', array('uses' => 'CartController@add'));

	Route::post('/update', array('uses' => 'CartController@update'));

	Route::post('/delete', array('uses' => 'CartController@delete'));

	Route::post('/destroy', array('uses' => 'CartController@destroy'));
	
	#Route::get('/checkout', array('before' => 'auth','uses' => 'CartController@checkout'));

	Route::get('/checkout', array('before' => 'auth', function(){

		//Evalúo si existe la variable get con el token del step 2
		if(empty($_GET['token-id'])){
			//Si no existe, envío a controlador para traer los datos del pedido
			$action = 'checkout';
        	return App::make('CartController')->$action();
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
						        <body style="background-image:url(http://pidafacil.com/images/background.jpg); background-repeat: no-repeat;">
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
							            <input type ="hidden" name="total_previo" value="'.Session::get('total_order').'">
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
						        <body style="background-image:url(http://pidafacil.com/images/background.jpg); background-repeat: no-repeat;">
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
				        return Redirect::to('cart/checkout')
                            ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				        break;
				    case 201:
				        return Redirect::to('cart/checkout')
                            ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				        break;
				    case 202:
				        return Redirect::to('cart/checkout')
                            ->withErrors("Lo sentimos, Fondos insuficientes. Intente con otro método de pago.");
				        break;
				    case 203:
				        return Redirect::to('cart/checkout')
                            ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				        break;
				    case 204:
				        return Redirect::to('cart/checkout')
                            ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				        break;
				    case 220:
				        return Redirect::to('cart/checkout')
                            ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				        break;
				    case 221:
				        return Redirect::to('cart/checkout')
                            ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				        break;    
				    case 222:
				        return Redirect::to('cart/checkout')
                            ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				        break;            
				    case 223:
				        return Redirect::to('cart/checkout')
                            ->withErrors("Lo sentimos, Su tarjeta esta expirada. Intente con otro método de pago.");
				        break;
				    case 224:
				        return Redirect::to('cart/checkout')
                            ->withErrors("Fecha de vencimiento inválida, verifique sus datos");
				        break;
				    case 225:
				        return Redirect::to('cart/checkout')
                            ->withErrors("Código de serguridad inválido, verifique sus datos");
				        break;  
				    case 240:
				        return Redirect::to('cart/checkout')
                            ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				        break;
				    case 250:
				        return Redirect::to('cart/checkout')
                            ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				    	break;
				    case 251:
				    	$idusuario = Auth::id();
				        $user = User::find($idusuario);
				        $user->sospecha = 1; 
				        $user->save();
				        Log::info('Usuario a sospecha: '. $user->user_id );
				        return Redirect::to('cart/checkout')
                            ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				        break; 
				    case 252:
				        $idusuario = Auth::id();
				        $user = User::find($idusuario);
				        $user->sospecha = 1; 
				        $user->save();
				        Log::info('Usuario a sospecha: '. $user->user_id );
				        return Redirect::to('cart/checkout')
                            ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				        break;
				    case 253:
				        $idusuario = Auth::id();
				        $user = User::find($idusuario);
				        $user->sospecha = 1;
				        $user->save();
				        Log::info('Usuario a sospecha: '. $user->user_id );
				        return Redirect::to('cart/checkout')
                            ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				        break; 
				    case 260:
				        return Redirect::to('cart/checkout')
                            ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				        break;       
				    case 261:
				        return Redirect::to('cart/checkout')
                        	->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				        break; 
				    case 262:
				        return Redirect::to('cart/checkout')
                            ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				        break;
				    case 263:
				        return Redirect::to('cart/checkout')
                            ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				        break;
				    case 264:
				        return Redirect::to('cart/checkout')
                            ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				        break;
				    case 300:
				        return Redirect::to('cart/checkout')
                            ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				        break; 
				    case 400:
				        return Redirect::to('cart/checkout')
                            ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				        break; 
				    case 410:
				        return Redirect::to('cart/checkout')
                            ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				        break;  
				    case 411:
				        return Redirect::to('cart/checkout')
                            ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				        break;
				    case 420:
				        return Redirect::to('cart/checkout')
                            ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				        break;                               
				    case 421:
				        return Redirect::to('cart/checkout')
                            ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				        break;                               
				    case 430:
				        return Redirect::to('cart/checkout')
                            ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				        break;                               
				    case 440:
				        return Redirect::to('cart/checkout')
                            ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				        break;                               
				    case 441:
				        return Redirect::to('cart/checkout')
                            ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				        break;                               
				    case 460:
				        return Redirect::to('cart/checkout')
                            ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				        break;  
				    case 461:
				        return Redirect::to('cart/checkout')
                            ->withErrors("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
				        break;                                 
				    
				    default:
				        # sino es ninguno de los parametros actuales de BAC
				        return Redirect::to('cart/checkout')
                            ->withErrors("Error en la Transacción bancaria. Intente con otro método de pago.");
				        break;
				}
		    }elseif((string)$gwResponse->result == 2){
		    	Log::info('Resultado Error: '.$gwResponse->{'result-code'});
		    	Log::info('Resultado Auth: '.$gwResponse->{'authorization-code'});
		    	Log::info('Resultado text: '.$gwResponse->{'result-text'});

		        return Redirect::to('cart/checkout')
                    ->withErrors("Lo sentimos, su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
		    }else{
		    	Log::info('Resultado 3: '.$gwResponse->{'result-code'});
		        return Redirect::to('cart/checkout')
                    ->withErrors('En este momento su transacción no puede ser realizada por el Banco. Intente con otro método de pago');
		    }
		}
	}));

	Route::post('/create2', array('uses' => 'OrderController@store'));
	
	Route::get('luhn', function(){

	    //Let's run the validator and set our rules
	    $validation = Validator::make(
	        [
	            'credit_card'  => '7907802606841797'    
	        ],
	        [
	            'credit_card'  => 'luhn',    
	        ]
	    );

	    //Did it pass?    
	    if($validation->passes()) {
	        return 'success!';
	    } else {
	        //Let's return the error code:
	        return dd($validation->errors());
	    }

	});

	Route::post('bines',array('uses' => 'CartController@bines'));
});
