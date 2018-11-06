<?php
Route::group(array('before' => 'auth_visor', 'prefix'=>'restaurant-orders/'), function()
{
	Route::get('', array('uses' => 'RestaurantOrdersController@index'));

	Route::get('{id}', array('before'=>'role_visor','uses' => 'RestaurantOrdersController@show'));

	Route::get('order/{id}', array('uses' => 'RestaurantOrdersController@order'));

    //Ruta para aceptar un pedido
	Route::post('forward', array('uses' => 'RestaurantOrdersController@forward'));

	Route::post('backward/{id}', array('uses' => 'RestaurantOrdersController@backward'));

    //Ruta para cambiar de estado, a cancelado, rechasada e incobrable
	Route::post('cancel', array('uses' => 'RestaurantOrdersController@cancel'));

    //Enviar correos informando el cambio de estado
    Route::Post('cambioEstado', array('uses' => 'EmailController@cambioEstado'));

	Route::post('baddebt/{id}', array('uses' => 'RestaurantOrdersController@baddebt'));

	Route::post('reject/{id}', array('uses' => 'RestaurantOrdersController@reject'));

	Route::post('accept', array('uses' => 'RestaurantOrdersController@accept'));

    //Actualizar restaurante(sucrusal) de la orden
    Route::post('asignar', array('uses' => 'RestaurantOrdersController@asignar'));

    //Tomar el tiempo
    Route::post('time', array('uses' => 'RestaurantOrdersController@time'));

});
Route::group(array('before' => 'role_pidafacil', 'prefix'=>'delivery_pidafacil/'), function()
{
    Route::get('', array('uses' => 'RestaurantOrdersController@deliveryPidafacil'));
});