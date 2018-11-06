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
    /*Route::get('/', function(){
       return Redirect::to('/restauran_login');
    });*/
    
    if (Auth::check()){
        if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 3) {
            Route::get('/', array('before' => 'guest', function(){
                return View::make('web.visor');
            }));
        }elseif(Auth::user()->role_id == 4) {
            Route::get('/', array('before' => 'guest', function(){
                return View::make('web.delivery_pidafacil');
            }));
        }
    }else{
        Route::get('/', function(){
           return Redirect::to('/restauran_login');
        });
    }

/*Route::get('/', function()
{
	return View::make('web.home');
});*/

// Dynamically include all files in the routes directory
/*foreach (new DirectoryIterator(__DIR__.'/routes') as $file){
    if (!$file->isDot() && !$file->isDir() && $file->getFilename() != '.gitignore'){
        require_once __DIR__.'/routes/'.$file->getFilename();
    }
}*/

// Luhn algorithm
Validator::resolver(function($translator, $data, $rules, $messages)  {
    return new ArdaValidator($translator, $data, $rules, $messages);
});

/*Todas las rutas utilizadas comienzan aquí*/
Route::get('/', array('uses' => 'UserController@showLoginVisor'));

Route::get('restauran_login/', array('uses' => 'UserController@showLoginVisor'));

Route::post('restauran_login/doLogin', array('uses' => 'UserController@doLogin'));

//Ruta para visor de restaurante
Route::get('restaurant-orders/{id}', array('before'=>'role_visor','uses' => 'RestaurantOrdersController@show'));

//Ruta para agregar observación a la orden
Route::post('restaurant-orders/comment', array('uses' => 'RestaurantOrdersController@addComment'));
Route::post('restaurant-orders/total_obs', array('uses' => 'RestaurantOrdersController@countObservacion'));
Route::post('restaurant-orders/observaciones', array('uses' => 'RestaurantOrdersController@allObservaciones'));

//Rutas para notificaciones en el visor
Route::post('restaurant-orders/new_ord', array('uses' => 'RestaurantOrdersController@new_orders'));
Route::post('restaurant-orders/new_ord_cc', array('uses' => 'RestaurantOrdersController@new_orders_cc'));
Route::post('restaurant-orders/new_ord_rest', array('uses' => 'RestaurantOrdersController@new_orders_rest'));

//Ruta para aceptar un pedido
Route::post('restaurant-orders/forward', array('uses' => 'RestaurantOrdersController@forward'));

//Tomar el tiempo
Route::post('restaurant-orders/time', array('uses' => 'RestaurantOrdersController@time'));

Route::get('/logout', array('uses' => 'UserController@doLogout'));

Route::group(array('before'=>'guest'), function() {   
    Route::controller('password', 'RemindersController');
});


Route::get('order/print/{id}', array('uses' => 'RestaurantOrdersController@order'));