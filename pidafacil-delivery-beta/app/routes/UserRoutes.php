<?php

Route::group(array('before' => 'guest', 'prefix'=>'/restauran_login'), function() {
    Route::get('/', array('uses' => 'UserController@showLoginVisor'));

    Route::post('/', array('uses' => 'UserController@doLogin'));
});
/*
Route::group(array('before' => 'guest', 'prefix'=>'/login'), function(){

	Route::get('/', array('uses' => 'UserController@showLogin'));

    Route::post('/', array('uses' => 'UserController@doLogin'));

	Route::get('/fb', array('uses' => 'UserController@fbLogin'));

	Route::get('/fb/callback', array('uses' => 'UserController@fbCallback'));


	Route::get('/gauth/{auth?}',array('as'=>'googleAuth','uses'=>'UserController@getGoogleLogin'));
	Route::get('/fbauth/{auth?}',array('as'=>'facebookAuth','uses'=>'UserController@getFacebookLogin'));

	/*
	Route::get('/', array('uses' => 'IngredientController@index'));

	Route::post('/create', array('uses' => 'IngredientController@store'));

	Route::get('/create', array('uses' => 'IngredientController@create'));

	Route::get('/{id}', array('uses' => 'IngredientController@show'));

	Route::get('/{id}/edit', array('uses' => 'IngredientController@edit'));

	Route::post('/{id}/edit', array('uses' => 'IngredientController@update'));

});*/

Route::get('/logout', array('uses' => 'UserController@doLogout'));
Route::post('/register', array('uses' => 'UserController@store'));

Route::group(array('before'=>'guest'), function() {   
    Route::controller('password', 'RemindersController');
});
/*
Route::group(array('before' => 'auth', 'prefix'=>'profile'), function()
{
	Route::get('/', array('uses' => 'UserController@show'));

	Route::post('/edit', array('uses' => 'UserController@update'));

});
Route::group(array('before' => 'auth', 'prefix'=>'user'), function()
{
	Route::get('/orders', array('uses' => 'OrderController@index'));

	Route::post('/repeat', array('uses' => 'OrderController@repeat'));

	// Addresses
	Route::get('/address', array('uses' => 'AddressController@index'));

	Route::post('/address/create', array('uses' => 'AddressController@create'));

	Route::post('/address/store', array('uses' => 'AddressController@store'));

	Route::post('/address/edit', array('uses' => 'AddressController@update'));

	Route::resource('address', 'AddressController',  array('only' => array('destroy')));
});*/

//Api Routes
Route::get('/email-check', array('uses' => 'UserController@emailCheck'));
Route::post('/social-connect', array('uses' => 'UserController@socialConnect'));
/*
Route::group(array('prefix'=>'api', 'before' => 'auth.basic'), function()
{
	Route::group(array('prefix'=>'login'), function()
	{
		Route::post('/', array('uses' => 'UserController@doLogin'));

		Route::get('/fb', array('uses' => 'UserController@fbLogin'));

		Route::get('/fb/callback', array('uses' => 'UserController@fbCallback'));
	});

	Route::group(array('prefix'=>'register'), function()
	{
		Route::post('/', array('uses' => 'UserController@store'));
	});
});
*/