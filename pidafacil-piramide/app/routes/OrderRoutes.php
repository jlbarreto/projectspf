<?php

Route::group(array('prefix'=>'order','before'=>'auth'), function()
{
	Route::get('/', array('uses' => 'OrderController@show'));

	Route::post('/create', array('uses' => 'OrderController@store'));

	Route::get('/{restslug}', array('uses' => 'OrderController@lista' ));

	Route::get('/{restslug}/{id}', array('uses' => 'OrderController@orden' ));
/*
	// Addresses
	Route::get('/address', array('uses' => 'AddressController@index'));

	Route::post('/address/create', array('uses' => 'AddressController@store'));
*/
	Route::post('/edit', array('uses' => 'OrderController@update'));

        Route::post('/shipping_charge', array('uses' => 'OrderController@shipping_charge' ));
});