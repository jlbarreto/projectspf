<?php
Route::group(array('prefix'=>'conditions'), function(){

		Route::group(array('prefix'=>'/{id}/'), function(){

			Route::get('edit', array('uses' => 'ConditionController@edit'));

			Route::post('edit', array('uses' => 'ConditionController@update'));
	
			Route::get('/', array('uses' => 'ConditionController@show'));

			Route::get('options', array('uses' => 'ConditionController@showoption'));

			Route::get('add', array('uses' => 'ConditionController@addoption'));

			Route::post('add', array('uses' => 'ConditionController@storeoption'));

			Route::post('delete', array('uses' => 'ConditionController@deleteoption'));

			Route::get('add-special', array('uses' => 'ConditionController@addspecialoption'));

			Route::post('add-special', array('uses' => 'ConditionController@storespecialoption'));

			Route::post('delete-special', array('uses' => 'ConditionController@deletespecialoption'));


	});

	//-----------------------------------------------------------------



	Route::get('/', array('uses' => 'ConditionController@index'));

	Route::post('/', array('uses' => 'ConditionController@store'));

	Route::get('/create', array('uses' => 'ConditionController@create'));

	Route::post('/delete', array('uses' => 'ConditionController@destroy'));


});
