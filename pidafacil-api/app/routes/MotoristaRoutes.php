<?php

// Before authentication
Route::group(array('before' => 'auth.basic', 'prefix'=>'motorista'), function()
{
    //Get orders
    Route::post('/orders', array('uses' => 'MotoristaController@get_orders'));

    //Get user by motorista_id
    Route::post('/show', array('uses' => 'MotoristaController@show'));

    //Do Login
    Route::post('/login', array('uses' => 'MotoristaController@doLogin'));     
});
