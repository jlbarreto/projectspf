<?php

// Before authentication
Route::group(array('before' => 'auth.basic', 'prefix'=>'mensajero'), function(){
    //Get status
    Route::post('/get_status_log', array('uses' => 'MensajeroStatusLogController@getMensajeroStatusLog'));

    //Update status
    Route::post('/update_status_log', array('uses' => 'MensajeroStatusLogController@updateMensajeroStatusLog'));

    //Add status
    Route::post('/store_status_log', array('uses' => 'MensajeroStatusLogController@storeMensajeroStatusLog'));     
});
