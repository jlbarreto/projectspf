<?php

// Before authentication
Route::group(array('prefix' => 'order'), function(){
	// Create order
	Route::post('/create/', array('uses' => 'OrderController@store'));

	// Show order details
	Route::post('/get/{order_id}', array('uses' => 'OrderController@show'));

  	//Show shipping charge
  	Route::post('/shipping_charge/{datos}', array('uses' => 'OrderController@shipping_charge'));

  	//Change status order
  	Route::post('/change_status', array('uses' => 'OrderController@change_status'));

  	Route::post('/create2', array('uses' => 'OrderController@store2'));
});
