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

Route::get('/', function()
{
	return View::make('web.log_visor');
});

/****************Ruta para logueo*******************/
Route::post('doLogin', array('uses' => 'UserController@doLogin'));

Route::get('logout', array('uses' => 'UserController@doLogout'));

/****************Fin Rutas logueo*******************/

//Ruta hacia el visor
Route::get('/delivery_pidafacil', array('before' => 'auth', 'uses' => 'RestaurantOrdersController@visorDelivery'));

//Tomar el tiempo
Route::post('time', array('uses' => 'RestaurantOrdersController@time'));

//Ruta para verificar los tiempos de las ordenes asignadas
Route::post('total_tiempo', array('uses' => 'RestaurantOrdersController@time_order'));

//Ruta para nuevas ordenes
Route::post('nuevas_ordenes_visor', array('uses' => 'RestaurantOrdersController@ordenes_asignadas'));

//Ruta para vista de reportes
Route::get('delivery_reportes', array('before' => 'auth', 'uses' => 'RestaurantOrdersController@reportes'));

//Ruta para traer resultado de busqueda en reportes
Route::post('busqueda_reporte', array('uses' => 'RestaurantOrdersController@busqueda_ordenes'));

//Ruta para vista de reporte detallado de ventas actual 23Mayo
Route::get('ventas_detalle', array('uses' => 'ReporteController@vista_rv'));

//Ruta para generar reporte detallado de ventas actual 23May
Route::post('rep_vent',array('uses' => 'ReporteController@reporte_ventas'));

//Ruta para exportar los datos a excel
Route::post('exportar_datos', array('uses' => 'ReporteController@exportarDatos'));

//Ruta para vista de reporte de ventas
Route::get('ventas', array('uses' => 'ReporteController@ventaEj'));

//Ruta para generar reporte de ventas
Route::post('ventaRep', array('uses' => 'ReporteController@ventasReporte'));

//Ruta para exportar a excel
Route::post('exportRep', array('uses' => 'ReporteController@reporteExcel'));

//Ruta para exportar excel de ventas detallada
Route::post('exportRepDet', array('uses' => 'ReporteController@reporteExcelDet'));

//Ruta para vista de reporte de iva por restaurante
Route::get('reporte_iva', array('uses' => 'ReporteController@viewIva'));

//Ruta para traer el reporte de iva por restaurante
Route::post('datos_iva', array('uses' => 'ReporteController@reporteIva'));

//Ruta para exportar reporte de iva
Route::post('exportIva', array('uses' => 'ReporteController@excelIva'));

/**********RUTAS PARA NUEVO USUARIO CARRIER***********/

//Ruta para reportes de ventas
Route::get('delivery_reportes_rest', array('uses' => 'CarrierRestaurantController@view_pedidos_rest'));

//Ruta para venta por mes y rango de fechas
Route::get('ventas_rest', array('uses' => 'CarrierRestaurantController@view_reporteV_rest'));

//Ruta para reporte de detalle de ventas
Route::get('ventas_detalle_rest', array('uses' => 'CarrierRestaurantController@view_reporteD_rest'));

//Ruta para reporte de tiempos
Route::get('reporte_tiempo_rest', array('uses' => 'CarrierRestaurantController@view_tiempos_rest'));

//Ruta para generar el reporte por fechas
Route::post('busqueda_reporte_rest', array('uses' => 'CarrierRestaurantController@generarReporte_rest'));

//Ruta para generar reporte de ventas por fechas y mes
Route::post('ventaRep_rest', array('uses' => 'CarrierRestaurantController@generar_reporteV_rest'));

//Ruta para generar reporte detallado
Route::post('rep_detalle_rest', array('uses' => 'CarrierRestaurantController@generar_reporteD_rest'));

//Ruta para generar reporte de tiempos de cada orden
Route::post('rep_tiempos_rest', array('uses' => 'CarrierRestaurantController@generarReporteT_rest'));

//Ruta para exportar todos los productos, secciones, tags y restaurantes
Route::get('exportar_prod', array('uses' => 'ExcelController@excelProductos'));


//Ruta para nuevo reporte - 06 abril 2017
Route::get('rest_pago', array('uses' => 'ReporteController@view_rest_pago'));

//Ruta para traer el reporte de restaurante, tipo pago, fechas - 06 abril 2017
Route::post('datos_rest_pago', array('uses' => 'ReporteController@reporte_Rest_Pago'));

//Ruta para exportar reporte de restaurante pago y fechas
Route::post('exportRestPago', array('uses' => 'ExcelController@excelRest_Pago_Fecha'));

//Ruta para ir a la vista de configuracion
Route::get('config', array('uses' => 'ConfigController@viewConfig'));

//Ruta para ir a la vista de activar o descartivar la promo
Route::get('promo', array('uses' => 'ConfigController@viewPromo'));

//Ruta para subir el archivo csv
Route::post('uploadCsv', array('uses' => 'ConfigController@uploadFile'));

//Ruta para agregar la promoción(incluye el porcentaje y activación)
Route::post('addPromo', array('uses' => 'ConfigController@newPromo'));

//Ruta para listar todas las promociones
Route::get('listPromo', array('uses' => 'ConfigController@listaPromociones'));

//Ruta para traer datos a modificar de la promo
Route::post('datosPromo', array('uses' => 'ConfigController@getDataPromo'));

//Ruta para actualizar datos de promo
Route::post('editarPromo', array('uses' => 'ConfigController@updatePromo'));

//Ruta para eliminar una promocion
Route::post('deletePromo', array('uses' => 'ConfigController@eliminarPromo'));