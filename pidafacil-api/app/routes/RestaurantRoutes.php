<?php

// Before authentication
Route::group(array('before' => 'auth.basic', 'prefix' => 'restaurant'), function()
{
	// Categories by type
	Route::post('/categories', array('uses' => 'RestaurantController@categories'));

	// Restaurants by category
	Route::post('/', array('uses' => 'RestaurantController@index'));

	// Show restaurant and web content
	Route::post('/get', array('uses' => 'RestaurantController@show'));

  Route::post('/getInfo', array('uses' => 'RestaurantController@info'));

	// Show restaurants with promos
	Route::post('/promos', array('uses' => 'RestaurantController@promos'));

	// Show menu sections
	Route::post('/sections', array('uses' => 'RestaurantController@sections'));

	// Show products from menu
	Route::post('/products', array('uses' => 'RestaurantController@products'));

	// Show address restaurant
	Route::post('/address', array('uses' => 'RestaurantController@addresses'));

	// Show address restaurant
	Route::post('/schedule', array('uses' => 'RestaurantController@schedule'));

	Route::post('/service-types', array('uses' => 'RestaurantController@service_types'));

	Route::post('/payment-methods', array('uses' => 'RestaurantController@payment_methods'));

	Route::post('/update-coordinates', array('uses' => 'RestaurantController@update_coordinates'));

	// Show all restaurants availables
	Route::post('/list', array('uses' => 'RestaurantController@restaurants_list'));

});
