<?php

Route::group(array('prefix'=>'cart'), function()
{

	Route::get('/', array('uses' => 'CartController@index'));

	Route::post('/add', array('uses' => 'CartController@add'));

	Route::post('/update', array('uses' => 'CartController@update'));

	Route::post('/delete', array('uses' => 'CartController@delete'));

	Route::post('/destroy', array('uses' => 'CartController@destroy'));
	
	Route::get('/checkout', array('before' => 'auth','uses' => 'CartController@checkout'));
	
	Route::get('luhn', function(){

	    //Let's run the validator and set our rules
	    $validation = Validator::make(
	        [
	            'credit_card'  => '7907802606841797'    
	        ],
	        [
	            'credit_card'  => 'luhn',    
	        ]
	    );

	    //Did it pass?    
	    if($validation->passes()) {
	        return 'success!';
	    } else {
	        //Let's return the error code:
	        return dd($validation->errors());
	    }

	});
});
