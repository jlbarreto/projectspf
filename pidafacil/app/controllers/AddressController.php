<?php

class AddressController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(){
		$date = new DateTime();
        $fecha = $date->format('Y-m-d H:i:s');

		$promociones = Product::where('activate',1)
		->where('promotion',1)
		->where('res_products.start_date', '<=', $fecha)->where('res_products.end_date', '>=', $fecha)
		->count();
		
		$user_id = Auth::id();
		$data['addresses']= Address::where('user_id', $user_id)->with('zone')->get();
                $statelst = State::where('country_id', 69)->where('active', true)->get();
                $data['states'] = array();
                $data['municipalities'] = array();
                $data['zones'] = array();
                $data['states']['']="--Seleccione un departamento--";
                $data['municipalities']['']="--Seleccione un municipio--";
                $data['zones']['']="--Seleccione una zona--";

                //Obtengo todas las zonas sin necesidad de los municipios
                /*$zonas = DB::table('pf.com_zones')
                ->join('pf.com_shipping_prices', 'pf.com_zones.shipping_price_id', '=', 'pf.com_shipping_prices.shipping_price_id')
                ->lists('zone','price','zone_id');*/

                $zonas = Zone::select('zone_id', DB::raw('CONCAT(zone, " - $", price) AS name_zona'))
			    ->join('pf.com_shipping_prices', 'pf.com_zones.shipping_price_id', '=', 'pf.com_shipping_prices.shipping_price_id')
			    ->lists('name_zona', 'zone_id');
                
                //$zonas = Zone::all()->lists('zone', 'zone_id');
                #sort($zonas);
		        $combobox = array('none' => "--Seleccione una zona") + $zonas;
		        $selected = array();

                foreach ($statelst as $state) {
                    $data['states'][$state->state_id]=$state->state;
                }
		//dd($statelst);
		return View::make('web.user_directions', compact('combobox', 'selected'))
			->with('promociones', $promociones)
            ->with($data);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create(){
		$validator = Address::validation(Input::all());

		if($validator->fails()) {
			$response['status']     = false;
			$response['data']	= $validator->messages();
		}else{

			$coordenadas = Input::get('coordenadas');
			Log::info('COORDENADAS '.$coordenadas);

			if($coordenadas != ''){

				$addrss = new Address;
				$addrss->user_id		= Auth::user()->user_id;
				$addrss->map_coordinates = Input::get('coordenadas');
				$addrss->address_name 	= Input::get('address_name');
				$addrss->address_1 		= Input::get('address_1');
				$addrss->reference 		= Input::get('reference');
				$addrss->zone_id		= Input::get('zone_id');
	            $addrss->country_id     = 69;
				
				$addrss->save();
			}else{
				$addrss = new Address;
				$addrss->user_id		= Auth::user()->user_id;
				$addrss->map_coordinates = '0.0,0.0';
				$addrss->address_name 	= Input::get('address_name');
				$addrss->address_1 		= Input::get('address_1');
				$addrss->reference 		= Input::get('reference');
				$addrss->zone_id		= Input::get('zone_id');
	            $addrss->country_id     = 69;
				
				$addrss->save();
			}
			
			$addrs = Address::where('user_id', Auth::user()->user_id)->get();
            $response['status'] = true;
		}
            return $response;
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(){
		$validator = Address::validation(Input::all());

		if($validator->fails()) {
			return Redirect::to('user/address')
            	->withErrors($validator)
            	->withInput();
		}else{
			$coordenadas = Input::get('coordenadas');

			if($coordenadas != ''){

				$addrss = new Address;
				$addrss->user_id		= Auth::user()->user_id;
				$addrss->map_coordinates = Input::get('coordenadas');
				$addrss->address_name 	= Input::get('address_name');
				$addrss->address_1 		= Input::get('address_1');
				$addrss->reference 		= Input::get('reference');
				$addrss->zone_id		= Input::get('zone_id');
	            $addrss->country_id     = 69;
				
				$addrss->save();
			}else{
				$addrss = new Address;
				$addrss->user_id		= Auth::user()->user_id;
				$addrss->address_name 	= Input::get('address_name');
				$addrss->address_1 		= Input::get('address_1');
				$addrss->reference 		= Input::get('reference');
				$addrss->zone_id		= Input::get('zone_id');
	            $addrss->country_id     = 69;
				
				$addrss->save();
			}

			//$addrs = Address::where('user_id', Auth::user()->user_id)->get();

			return Redirect::to('user/address')->with('message', 'Direccion agregada!');
		}
		return Redirect::to('user/address');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id){
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id){
		$response['address'] = Address::findOrFail($id);
        $response['zone'] = Zone::findOrFail($response['address']->zone_id);
        $response['municipality'] = Municipality::findOrFail($response['zone']->municipality_id);
        return $response;
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update(){
		$validator = Address::validation(Input::all());

		if($validator->fails()) {
			$response['status']     = false;
			$response['data']	= $validator->messages();
		}else{
			$addrss = Address::findOrFail(Input::get('address_id'));
			//$addrss->user_id	= Input::get('user_id');//Auth::user()->user_id;
			//$addrss->map_coordinates = Input::get('map_coordinates');
			$addrss->address_name 	= Input::get('address_name');
			$addrss->address_1 		= Input::get('address_1');
			#$addrss->address_2 		= Input::get('address_2');
			$addrss->reference 		= Input::get('reference');
			$addrss->zone_id		= Input::get('zone_id');
			$addrss->country_id 	= 69;
			// $addrss->country_id 	= Input::get('country_id');
			$addrss->save();

			$response['status'] = true;
			$response['data']	= "Dirección actualizada con éxito";
		}
		return $response;
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id){
		$addrss = Address::find($id);
		$addrss->delete();

		// redirect
		return Redirect::to('user/address')->with('message', 'Direccion eliminada!');
	}

    /**
     * Devolviendo zonas de la base de datos por una municipality_id
     */
    public function getZonesByMunicipality(){

        $statusCode = 200;
        $input = Input::all();
        try {

            $municipality = Municipality::findOrFail($input['municipality_id']);
            $lst = array();
            $zonesLst = $municipality->zones()->get();

            foreach ($zonesLst as $z) {
                $lst[] = array(
                    'zone_id' => $z->zone_id,
                    'zone' => $z->zone,
                    'shipping_change' => $z->shipping_charge
                );
            }

            $response = array(
                "status" => True,
                "data" => $lst
            );
        } catch (Exception $e) {
            $statusCode = 400;
            $response = array(
                "status" => false,
                "data" => $e->getMessage()
            );
        }

        return Response::json($response, $statusCode);
    }

    function getMunicipalities() {
        $input = Input::all();
        try {
            $statusCode = 200;
            $state = State::findOrFail($input['state_id']);

            $lst = $state->municipalities()->get();

            $response = array(
                "status" => True,
                "data" => $lst
            );
        } catch (Exception $e) {
            $statusCode = 400;
            $response = array(
                "status" => false,
                "data" => $e->getMessage()
            );
        }
        return Response::json($response, $statusCode);
    }

    function getStates() {
        $input = Input::all();
        try {
            $statusCode = 200;

            $lst = State::where('country_id', $input['country_id'])
                    ->with('municipalities')
                    //->join('com_zones', 'com_municipalities.municipality_id', '=', 'com_zones.municipality.id')
                    ->get();

            for ($i=0; $i<count($lst); $i++) {
                for ($j=0; $j<count($lst[$i]->municipalities); $j++) {
                    $lst[$i]->municipalities[$j]->zones=Zone::where('municipality_id', $lst[$i]->municipalities[$j]->municipality_id)->get();
                }
            }

            $response = array(
                "status" => True,
                "data" => $lst
            );
        } catch (Exception $e) {
            $statusCode = 400;
            $response = array(
                "status" => false,
                "data" => $e->getMessage()
            );
        }
        return Response::json($response, $statusCode);
    }

}
