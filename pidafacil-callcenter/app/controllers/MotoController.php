<?php

class MotoController extends BaseController {

	public function newVista(){
		$motoristas = Motorista::orderBy('motorista_id', 'ASC')->where('active', 1)->get();

		return View::make('web.mantenimiento_motos')
			->with('motoristas',$motoristas);
	}

	public function addMoto(){
		$nombre = Input::get('nombres');
		$apellido = Input::get('apellidos');
		$telefono1 = Input::get('telefono1');
		$telefono2 = Input::get('telefono2');
		$dui = Input::get('dui');
		$nit = Input::get('nit');
		$direccion = Input::get('direccion');
		$nombre_emergencia = Input::get('nombre_emergencia');
		$tel_emergencia = Input::get('telefono_emergencia');
		$motorista_id = Input::get('motoSelect');

		$destinationPath1 = '';
	    $filename1 = '';

	    $destinationPath2 = '';
	    $filename2 = '';

	    $destinationPath3 = '';
	    $filename3 = '';

	    $destinationPath4 = '';
	    $filename4 = '';

	    if (Input::hasFile('img_dui')) {
	        $file = Input::file('img_dui');
	        $destinationPath1 = public_path().'/img/';
	        $filename1 = $motorista_id.'_'.$file->getClientOriginalName();
	        $uploadSuccess = $file->move($destinationPath1, $filename1);
	    }

	    if (Input::hasFile('img_lic')) {
	        $file = Input::file('img_lic');
	        $destinationPath2 = public_path().'/img/';
	        $filename2 = $motorista_id.'_'.$file->getClientOriginalName();
	        $uploadSuccess = $file->move($destinationPath2, $filename2);
	    }

	    if (Input::hasFile('img_antecedentes')) {
	        $file = Input::file('img_antecedentes');
	        $destinationPath3 = public_path().'/img/';
	        $filename3 = $motorista_id.'_'.$file->getClientOriginalName();
	        $uploadSuccess = $file->move($destinationPath3, $filename3);
	    }

	    if (Input::hasFile('img_solvencia')) {
	        $file = Input::file('img_solvencia');
	        $destinationPath4 = public_path().'/img/';;
	        $filename4 = $motorista_id.'_'.$file->getClientOriginalName();
	        $uploadSuccess = $file->move($destinationPath4, $filename4);
	    }

		$moto = new DetalleMotorista;
		$moto->nombres = $nombre;
		$moto->apellidos = $apellido;
		$moto->direccion = $direccion;
		$moto->dui = $dui;
		$moto->nit = $nit;
		$moto->telefono = $telefono1;
		$moto->telefono2 = $telefono2;
		$moto->emergencia_llamar_a = $nombre_emergencia;
		$moto->telefono_emergencia = $tel_emergencia;
		$moto->motorista_id = $motorista_id;
		$moto->foto_dui = $destinationPath1 . $filename1;
		$moto->foto_licencia = $destinationPath2 . $filename2;
		$moto->foto_antecedentes = $destinationPath3 . $filename3;
		$moto->foto_solvencia = $destinationPath4 . $filename4;
		$moto->save();

		return Redirect::to('listMoto');
	}

	public function allMotos(){

		$motos = DetalleMotorista::get();
		
		return View::make('web.listadoMotos')
			->with('motos',$motos);
	}

	public function obtenerDatos(){

		if(Request::ajax()){

			$detalle = Input::get('detalle_id');

			$datos = DetalleMotorista::where('detalle_id', '=', $detalle)->get();
			Log::info($datos);

			return Response::json($datos);
		}
	}

	public function editarMoto(){

		if(Request::ajax()){

			$detalle = Input::get('detalle_id');
			$nombre = Input::get('nombres');
			$apellido = Input::get('apellidos');
			$telefono1 = Input::get('telefono1');
			$telefono2 = Input::get('telefono2');
			$dui = Input::get('dui');
			$nit = Input::get('nit');
			$direccion = Input::get('direccion');
			$nombre_emergencia = Input::get('nombre_emergencia');
			$tel_emergencia = Input::get('telefono_emergencia');
			$moto = Input::get('moto_id');

			$moto = DetalleMotorista::find(Input::get('detalle_id'));
            $moto->nombres = $nombre;
			$moto->apellidos = $apellido;
			$moto->direccion = $direccion;
			$moto->dui = $dui;
			$moto->nit = $nit;
			$moto->telefono = $telefono1;
			$moto->telefono2 = $telefono2;
			$moto->emergencia_llamar_a = $nombre_emergencia;
			$moto->telefono_emergencia = $tel_emergencia;
			$moto->save();

			return Response::json(Input::get('detalle_id'));
		}
	}

	public function eliminarMoto(){

		if(Request::ajax()){

			$detalle_id = Input::get('detalle_id');

			$detalle = DetalleMotorista::find($detalle_id);
			$detalle->delete();

			return Response::json($detalle_id);
		}
	}
}