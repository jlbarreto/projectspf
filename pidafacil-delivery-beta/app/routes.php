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
       return Redirect::to('/restauran_login');
    });
/*Route::get('/', function()
{
	return View::make('web.home');
});*/

// Dynamically include all files in the routes directory
foreach (new DirectoryIterator(__DIR__.'/routes') as $file)
{
    if (!$file->isDot() && !$file->isDir() && $file->getFilename() != '.gitignore')
    {
        require_once __DIR__.'/routes/'.$file->getFilename();
    }
}

/*
// Display all SQL executed in Eloquent
Event::listen('illuminate.query', function($query)
{
    var_dump($query);
});*/

// Luhn algorithm
Validator::resolver(function($translator, $data, $rules, $messages)  
{
    return new ArdaValidator($translator, $data, $rules, $messages);
});