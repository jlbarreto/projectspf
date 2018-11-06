<?php

// Before authentication
Route::group(array('before' => 'auth.basic', 'prefix' => 'zones'), function()
{
	// Show all zones
	Route::post('/get', array('uses' => 'AddressController@getZones'));
	// Show zones by municipality
	Route::post('/byMunicipality', array('uses' => 'AddressController@getZonesByMunicipality'));
});
