<?php
	//Route::post('/generalOption', array('uses' => 'GeneralOptionsController@getGeneralOptions'));
	Route::group(array(), function(){
		
		//Route::get('/generalOption', array('uses' => 'GeneralOptionsController@getGeneralOptions'));		
		
		Route::get('generalOption', function(){
		  $pfOptions = GeneralOptions::findOrFail(1);
			return json_encode($pfOptions);
		});
	});
?>