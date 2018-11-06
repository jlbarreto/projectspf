<?php

// Before authentication
//Route::group(array('before' => 'auth.basic', 'prefix' => 'product'), function()
Route::group(array('prefix' => 'product/'), function(){

	Route::post('get/{prodID}', array('uses' => 'ProductController@show'));

	Route::post('/promos', array('uses' => 'ProductController@promos'));

  	Route::post('/restaurant', array('uses' => 'ProductController@restaurant'));
});
