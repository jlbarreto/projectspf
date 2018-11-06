<?php

Route::group(array('prefix'=>'order','before'=>'auth'), function()
{
	Route::get('/', array('uses' => 'OrderController@show'));

	Route::post('/create', array('uses' => 'OrderController@store'));

	Route::get('/{restslug}', array('uses' => 'OrderController@lista' ));

	Route::get('/{restslug}/{id}', array('uses' => 'OrderController@orden' ));

	Route::post('/edit', array('uses' => 'OrderController@update'));

});
