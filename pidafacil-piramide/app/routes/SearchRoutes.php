<?php

Route::get('/promociones/autocomplete', array('uses' => 'SearchController@autocomplete'));
Route::get('/tags-search/{tag}', array('uses' => 'SearchController@tags'));
Route::get('/search', array('uses' => 'SearchController@search'));
Route::get('/promociones/{tag?}', array('uses' => 'SearchController@promos'));
Route::get('/explorar/{tag?}', array('uses' => 'SearchController@explorar'));
