<?php

// Before authentication
Route::group(array('before' => 'auth.basic', 'prefix' => 'order'), function()
{
	// Create order
	Route::post('/create', array('uses' => 'OrderController@store'));

	// New route to create orders
	Route::post('/make', array('uses' => 'OrderController@makeStore'));

	// Show order details
	Route::post('/get', array('uses' => 'OrderController@show'));

  //Show shipping charge
  Route::post('/shipping_charge', array('uses' => 'OrderController@shipping_charge'));

  //Change status order
  Route::post('/change_status', array('uses' => 'OrderController@change_status'));

	// Verify BIN
	Route::post('/bin-verify', array('uses' => 'BinController@verify'));
});
