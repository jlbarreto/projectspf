<?php
Route::group(array('before' => 'guest', 'prefix'=>'/login'), function(){
	Route::get('/', array('uses' => 'UserController@showLogin'));

	Route::post('/', array('uses' => 'UserController@doLogin'));

	/*
	Route::get('/', array('uses' => 'IngredientController@index'));

	Route::post('/create', array('uses' => 'IngredientController@store'));

	Route::get('/create', array('uses' => 'IngredientController@create'));

	Route::get('/{id}', array('uses' => 'IngredientController@show'));

	Route::get('/{id}/edit', array('uses' => 'IngredientController@edit'));

	Route::post('/{id}/edit', array('uses' => 'IngredientController@update'));
	*/
});

Route::get('/logout', array('uses' => 'UserController@doLogout'));


Route::group(array('before' => 'auth', 'prefix'=>'profile'), function(){
	Route::get('/', array('uses' => 'UserController@show'));

	Route::post('/edit', array('uses' => 'UserController@update'));
});

//Ruta para archivo que evaluará crontab
Route::get('cronjob', array('uses' => 'CronController@verificar'));

