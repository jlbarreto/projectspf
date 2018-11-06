<?php
// Before authentication
//Ruta para procesar pago automático
Route::post('pago_auto', array('uses' => 'PagoController@createOrder'));

//Ruta auxiliar para pago automático
Route::get('step_three', array('uses' => 'PagoController@process_order'));

Route::get('paso1', array('as' => 'paso1', 'uses' => 'PagoController@step1'));

#Route::get('prueba', array('uses' => 'PagoController@test'));
/*Route::get("prueba", function(){
   return View::make("ionic.index");
});*/

//Login
Route::group(array('prefix'=>'login'), function(){

	Route::post('/{user}', array('uses' => 'UserController@doLogin'));

	Route::get('/fb', array('uses' => 'UserController@fbLogin'));

	Route::get('/fb/callback', array('uses' => 'UserController@fbCallback'));

});

//<<<<<<< HEAD
//Route::group(array(), function(){	
//=======
/*Route::group(array('before' => 'csrf'), function(){	
>>>>>>> 5831e26f54f41abc423e8d4822d3aea9f3745dc9*/

	//Register
	Route::group(array('prefix'=>'register'), function(){
		Route::post('/{datos}', array('uses' => 'UserController@store'));
	});

	//Api Routes
	Route::post('/email-check', array('uses' => 'UserController@emailCheck'));

	Route::post('/social-connect', array('uses' => 'UserController@socialConnect'));

	//Profile
	Route::group(array('prefix'=>'profile'), function(){
		Route::post('/{user_id}', array('uses' => 'UserController@show'));

		Route::post('/edit', array('uses' => 'UserController@update'));
	});

	Route::group(array('prefix'=>'user'), function(){
		// Addresses
		Route::post('/address/{user_id}', array('uses' => 'AddressController@index'));

		Route::post('/address/get/{address_id}', array('uses' => 'AddressController@show'));

		Route::post('/address/create', array('uses' => 'AddressController@store'));
                
            //Clone de create address por nuevos cambios
            //TODO: Eliminar la ruta y método anterior
		Route::post('/address/createNew/{direccion}', array('uses' => 'AddressController@storeNew'));

		Route::post('/address/edit', array('uses' => 'AddressController@update'));

		Route::post('/address/drop', array('uses' => 'AddressController@destroy'));

		// Orders
		Route::post('/orders/{datos}', array('uses' => 'OrderController@index'));
	});
//});

//Logout
Route::get('/logout', array('uses' => 'UserController@doLogout'));

/*
Route::get('/usergen', function(){
	
	$user = new User;
    $user->email 			= 'api@pidafacil.net';
    $user->password 		= Hash::make('qwerty');
    $user->country_id 		= 69; // El Salvador
    $user->status 			= 1; // Activo - Dev version
    $user->terms_acceptance = 1;
    $user->save();

    return "woohoo!!!";
});
*/
