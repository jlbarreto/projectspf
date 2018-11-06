<?php
Route::group(array('prefix'=>'{restslug}/ingredient'), function(){

	Route::get('/', array('uses' => 'IngredientController@index'));

	Route::post('/create', array('uses' => 'IngredientController@store'));

	Route::get('/create', array('uses' => 'IngredientController@create'));

	Route::get('/{id}', array('uses' => 'IngredientController@show'));

	Route::get('/{id}/edit', array('uses' => 'IngredientController@edit'));

	Route::post('/{id}/edit', array('uses' => 'IngredientController@update'));

	});