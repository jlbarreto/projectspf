<?php
//---- Rutas para poder acceder a Restaurante---------
Route::group(array('prefix'=>'restaurant'), function(){

	Route::get('/', array('uses' => 'RestaurantController@index'));

	Route::post('/', array('uses' => 'RestaurantController@store'));

	Route::get('/create', array('uses' => 'RestaurantController@create'));


	});
/*
Route::get('/{restslug}', array('uses' => 'RestaurantController@show'));

Route::get('{restslug}/schedule', array('uses' => 'RestaurantController@showshedules' ));*/
	//$restricciones = '^((?!conditions|payment-method|profile|login|logout).)*';

	//$restricciones = '^((?!order|conditions|payment-method|profile|login|logout|register|cart|promociones|user).)*';


	$restricciones = $restricciones = ParentRestaurant::restricciones();



	//----------------- Secciones-----------------------//
	Route::get('{restslug}/sections', array('uses' => 'SectionController@index'))->where('restslug', $restricciones);

	Route::get('{restslug}/sections/{id}', array('uses' => 'SectionController@show'))->where('restslug', $restricciones);

	Route::post('{restslug}/sections', array('uses' => 'SectionController@store'))->where('restslug', $restricciones);

	Route::get('{restslug}/sections/create', array('uses' => 'SectionController@create'))->where('restslug', $restricciones);

	Route::get('{restslug}/sections/{id}/edit', array('uses' => 'SectionController@edit'))->where('id', '[0-9]+')->where('restslug', $restricciones);

	Route::post('{restslug}/sections/{id}/update', array('uses' => 'SectionController@update'))->where('restslug', $restricciones);

	Route::get('{restslug}/sections/{id}/delete', array('uses' => 'SectionController@destroy'))->where('restslug', $restricciones);
	//----------------------------------------------------------------------------------------------------------------------

	//-----------------Horario----------------------//
	Route::get('{restslug}/schedule', array('uses' => 'RestaurantController@showschedule' ))->where('restslug', $restricciones);

	Route::post('{restslug}/schedule', array('uses' => 'RestaurantController@createschedule'))->where('restslug', $restricciones);

	Route::post('{restslug}/schedule/update/{id}', array('uses' => 'RestaurantController@storeschedule'))->where('restslug', $restricciones);

	Route::get('{restslug}/schedule/update/{id}', array('uses' => 'RestaurantController@updateschedule'))->where('restslug', $restricciones);

	Route::post('{restslug}/schedule/delete/{id}', array('uses' => 'RestaurantController@deleteschedule'))->where('restslug', $restricciones);

	//-----------------------------Web Content--------------------------------------------------------------

	Route::get('{restslug}/webcontent', array('uses' => 'WebcontentController@index'));

	Route::post('{restslug}/webcontent', array('uses' => 'WebcontentController@store'));

	Route::get('{restslug}/webcontent/create', array('uses' => 'WebcontentController@create'));

	Route::get('{restslug}/webcontent/edit', array('uses' => 'WebcontentController@edit'));

	Route::post('{restslug}/webcontent/update', array('uses' => 'WebcontentController@update'));

	//----------------------------------------------------------------------------------------------------------
	Route::get('{restslug}/about', array('uses' => 'RestaurantController@about'))->where('restslug', $restricciones);

	Route::get('{restslug}', array('uses' => 'RestaurantController@show'))->where('restslug', $restricciones);

	Route::get('{restslug}/payment-method', array('uses' => 'RestaurantController@paymentmethod'))->where('restslug', $restricciones);

	Route::get('{restslug}/edit', array('uses' => 'RestaurantController@edit'))->where('restslug', $restricciones);

	Route::post('{restslug}', array('uses' => 'RestaurantController@update'))->where('restslug', $restricciones);
	//-------------------------------------------------------------------------------------
	//-------------------Ordenes por restaurante-------------------------------------------
	
	Route::get('{restslug}/promociones', array('uses' => 'RestaurantController@promos'))->where('restslug', $restricciones);