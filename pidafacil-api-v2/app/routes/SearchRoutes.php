<?php

// Before authentication
Route::group(array('before' => 'auth.basic'), function()
{
	// Categories by type
	Route::post('autocomplete', array('uses' => 'SearchController@autocomplete'));
});