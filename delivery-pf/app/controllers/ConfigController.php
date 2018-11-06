<?php

class ConfigController extends \BaseController {

	public function viewConfig(){
		return View::make('web.configuracion');
	}

	public function viewPromo(){
		return View::make('web.promocion');
	}

	//Funcion para subir el archivo csv
	public function uploadFile(){

		$tipo = $_FILES['archivocsv']['type'];
		$tamanio = $_FILES['archivocsv']['size'];
		$archivotmp = $_FILES['archivocsv']['tmp_name'];
		 
		//cargamos el archivo
		$lineas = file($archivotmp);

		//inicializamos variable a 0, esto nos ayudará a indicarle que no lea la primera línea
		$i=0;
		 
		//Recorremos el bucle para leer línea por línea
		foreach ($lineas as $linea_num => $linea){ 
		   	//abrimos bucle
		   	/*si es diferente a 0 significa que no se encuentra en la primera línea 
		   	(con los títulos de las columnas) y por lo tanto puede leerla*/
		   	if($i != 0) {
		       //abrimos condición, solo entrará en la condición a partir de la segunda pasada del 	bucle.
		       	/* La funcion explode nos ayuda a delimitar los campos, por lo tanto irá 
		       	leyendo hasta que encuentre una , */
		       	$datos = explode(",",$linea);
		 
		       	//Almacenamos los datos que vamos leyendo en una variable
		       	$bin = trim($datos[0]);		       	
		 
		       	//guardamos en base de datos la línea leida
		 		$list = new ListBin;
				$list->num_bin = $bin;
				$list->save();
		       //cerramos condición
		   	}
		 
		   	/*Cuando pase la primera pasada se incrementará nuestro valor y a la siguiente pasada ya 
		   	entraremos en la condición, de esta manera conseguimos que no lea la primera línea.*/
		   	$i++;
		   	//cerramos bucle
		}

		return Redirect::to('config');
	}

	//Función para agregar una promoción y así evaluar los bins de tarjetas
	public function newPromo(){

		$porcentaje = Input::get("porcentaje");
		$activate = Input::get("activate");

		$list = new ConfigBin;
		$list->porcentaje = $porcentaje;
		$list->activate = $activate;
		$list->save();

		return Redirect::to('listPromo');		
	}

	//Funcion para traer todas las promociones guardadas
	public function listaPromociones(){
		
		//$promos = ConfigBin::get();
		$promos = DB::table('config_bins')->count();

		if ($promos == 0) {
			$result = "null";
		}else{
			$result = ConfigBin::get();
		}

		return View::make('web.lista_promo')
			->with('result',$result);
	}

	//Funcion para obtener datos para actualizar promo
	public function getDataPromo(){
		if(Request::ajax()){
			$id_promo = Input::get('promo_id');

			$datos = ConfigBin::where('id_config', '=', $id_promo)->get();

			Log::info($datos);

			return Response::json($datos);
		}
	}

	//Funcion para actualizar datos de la promo
	public function updatePromo(){
		if(Request::ajax()){
			$id_promo = Input::get('id_promo');
			$porcentaje = Input::get('porcentaje');
			$activate = Input::get('activate');

			$promo = ConfigBin::find(Input::get('id_promo'));
            $promo->porcentaje = $porcentaje;
			$promo->activate = $activate;
			$promo->save();

			return Response::json(Input::get('id_promo'));
		}	
	}

	public function eliminarPromo(){

		if(Request::ajax()){

			$id_promo = Input::get('id_promo');

			$promo = ConfigBin::find($id_promo);
			$promo->delete();

			return Response::json($id_promo);
		}
	}

}