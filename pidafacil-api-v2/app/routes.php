<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function(){
	return View::make('hello');
});

// Dynamically include all files in the routes directory
foreach (new DirectoryIterator(__DIR__.'/routes') as $file){
    if (!$file->isDot() && !$file->isDir() && $file->getFilename() != '.gitignore'){
        require_once __DIR__.'/routes/'.$file->getFilename();
    }
}

////Rutas para configuracion de PF

//Route::get('/generalOptions', array('uses' => 'GeneralOptionsController@getNumeroWhatsapp'));

// Returns the csrf token for the current visitor's session.
Route::get('csrf', function() {
	Log::info('ESTE TOKEN ENVIA LARAVEL '.Session::token());
    	return Session::token();
});

// Before making the declared routes available, run them through the api.csrf filter
Route::group(array('prefix' => 'api/v1', 'before' => 'api.csrf'), function() {
	Route::resource('test1', 'Api\V1\Test1Controller');
	Route::resource('test2', 'Api\V1\Test2Controller');
});
