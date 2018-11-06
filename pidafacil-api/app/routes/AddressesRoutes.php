<?php

// Before authentication
Route::group(array('before' => 'auth.basic', 'prefix' => 'addresses'), function()
{
	// Get municipalities from states
	Route::post('/municipalities', array('uses' => 'AddressController@getMunicipalities'));
        
	// Get States from country
	Route::post('/states', array('uses' => 'AddressController@getStates'));

	// Actualiza coordenadas de direccion
	Route::post('/update-coordinates', array('uses' => 'AddressController@update_coordinates'));

	// Get States from country
	Route::post('/zonesShipping', array('uses' => 'AddressController@getZonesShippingPrice'));

});