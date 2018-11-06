<?php
//---- Rutas para poder acceder a Restaurante---------
Route::group(array('prefix'=>'{restslug}/product'), function(){

	Route::get('/', array('uses' => 'ProductController@index'));

	Route::post('/create', array('uses' => 'ProductController@store'));

	Route::get('/create', array('uses' => 'ProductController@create'));

	});


Route::group(array('prefix'=>'{restslug}/'), function(){


	$restriccionesProductos = '^((?!ingredient|product|schedule|edit|conditions|payment-method|fb|sections|about|webcontent|gauth|checkout|orders|promociones|remind|reset).)*';

	//$restriccionesProductos = '^((?!ingredient|product|schedule|edit|conditions|payment-method|fb|sections|about|webcontent|gauth|checkout).)*';
	$restricciones = ParentRestaurant::restricciones();


	Route::get('{prodslug}/tags', array('uses' => 'ProductController@showtags'))->where('prodslug', $restriccionesProductos)->where('restslug', $restricciones);

	Route::post('{prodslug}/tags/{id}/delete', array('uses' => 'ProductController@deletetags'))->where('prodslug', $restriccionesProductos)->where('restslug', $restricciones);

	Route::post('{prodslug}/tags/{id}', array('uses' => 'ProductController@addtags'))->where('prodslug', $restriccionesProductos)->where('restslug', $restricciones);

	Route::get('{prodslug}', array('uses' => 'ProductController@show'))->where('prodslug', $restriccionesProductos)->where('restslug', $restricciones);

	Route::get('{prodslug}/edit', array('uses' => 'ProductController@edit'))->where('prodslug', $restriccionesProductos)->where('restslug', $restricciones);

	Route::post('{prodslug}/update', array('uses' => 'ProductController@update'))->where('prodslug', $restriccionesProductos)->where('restslug', $restricciones);

	Route::post('{prodslug}/delete', array('uses' => 'ProductController@destroy'))->where('prodslug', $restriccionesProductos)->where('restslug', $restricciones);

	Route::get('{prodslug}/ingredients', array('uses' => 'ProductController@showingredient'))->where('prodslug', $restriccionesProductos)->where('restslug', $restricciones);

	Route::post('{prodslug}/ingredients', array('uses' => 'ProductController@addingredient'))->where('prodslug', $restriccionesProductos)->where('restslug', $restricciones);

	Route::post('{prodslug}/ingredients/{id}/delete', array('uses' => 'ProductController@deleteingredient'))->where('prodslug', $restriccionesProductos)->where('restslug', $restricciones);

	Route::get('{prodslug}/conditions', array('uses' => 'ProductController@showconditions'))->where('prodslug', $restriccionesProductos)->where('restslug', $restricciones);

	Route::post('{prodslug}/conditions/{id}', array('uses' => 'ProductController@addconditions'))->where('prodslug', $restriccionesProductos)->where('restslug', $restricciones);

	Route::post('{prodslug}/conditions/{id}/delete', array('uses' => 'ProductController@deleteconditions'))->where('prodslug', $restriccionesProductos)->where('restslug', $restricciones);


	});



 //(?!pattern)