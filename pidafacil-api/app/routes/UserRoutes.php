<?php
// Before authentication
Route::group(array('before' => 'auth.basic'), function()
{
	//Login
	Route::group(array('prefix'=>'login'), function(){

		Route::post('/', array('uses' => 'UserController@doLogin'));

		Route::get('/fb', array('uses' => 'UserController@fbLogin'));

		Route::get('/fb/callback', array('uses' => 'UserController@fbCallback'));

	});

	//Register
	Route::group(array('prefix'=>'register'), function()
	{
		Route::post('/', array('uses' => 'UserController@store'));
	});

	//Api Routes
	Route::post('/email-check', array('uses' => 'UserController@emailCheck'));

	Route::post('/social-connect', array('uses' => 'UserController@socialConnect'));

	//Profile
	Route::group(array('prefix'=>'profile'), function()
	{
		Route::post('/', array('uses' => 'UserController@show'));

		Route::post('/edit', array('uses' => 'UserController@update'));

	});

	Route::group(array('prefix'=>'user'), function()
	{
		// Addresses
		Route::post('/address', array('uses' => 'AddressController@index'));

		Route::post('/address/get', array('uses' => 'AddressController@show'));

		Route::post('/address/create', array('uses' => 'AddressController@store'));
                
                //Clone de create address por nuevos cambios
                //TODO: Eliminar la ruta y método anterior
		Route::post('/address/createNew', array('uses' => 'AddressController@storeNew'));

		Route::post('/address/edit', array('uses' => 'AddressController@update'));

		Route::post('/address/drop', array('uses' => 'AddressController@destroy'));

		// Orders
		Route::post('/orders', array('uses' => 'OrderController@index'));

	});
});

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