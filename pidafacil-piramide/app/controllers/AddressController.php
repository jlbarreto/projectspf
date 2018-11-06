<?php

class AddressController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$user_id = Auth::id();
		$data['addresses']= Address::where('user_id', $user_id)->with('zone')->get();
                $statelst = State::where('country_id', 69)->where('active', true)->get();
                $data['states'] = array();
                $data['municipalities'] = array();
                $data['zones'] = array();
                $data['states']['']="--Seleccione un departamento--";
                $data['municipalities']['']="--Seleccione un municipio--";
                $data['zones']['']="--Seleccione una zona--";

                foreach ($statelst as $state) {
                    $data['states'][$state->state_id]=$state->state;
                }
		//dd($statelst);
		return View::make('web.user_directions')
                        ->with($data);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$validator = Address::validation(Input::all());

		if($validator->fails()) {
			$response['status']     = false;
			$response['data']	= $validator->messages();
		}else{
			$addrss = new Address;
			$addrss->user_id		= Auth::user()->user_id;
			//$addrss->map_coordinates = Input::get('map_coordinates');
			$addrss->address_name 	= Input::get('address_name');
			$addrss->address_1 		= Input::get('address_1');
			$addrss->address_2 		= Input::get('address_2');
			$addrss->reference 		= Input::get('reference');
			$addrss->zone_id		= Input::get('zone_id');
                        $addrss->country_id             = 69;
			// Input::get('country_id');
			$addrss->save();
			/*
			$response['status'] = true;
			$response['data']	= $addrss;
			*/
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
	public function store()
	{
		$validator = Address::validation(Input::all());

		if($validator->fails()) {
			return Redirect::to('user/address')
            	->withErrors($validator)
            	->withInput();
		}else{
			$addrss = new Address;
			$addrss->user_id		= Auth::user()->user_id;
			//$addrss->map_coordinates = Input::get('map_coordinates');
			$addrss->address_name 	= Input::get('address_name');
			$addrss->address_1 		= Input::get('address_1');
			$addrss->address_2 		= Input::get('address_2');
			$addrss->city 			= Input::get('city');
			$addrss->state 			= Input::get('state');
			$addrss->reference 		= Input::get('reference');
			$addrss->country_id 	= 69; // Input::get('country_id');
			$addrss->save();

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
	public function show($id)
	{
		//
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
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
	public function update()
	{
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
			$addrss->address_2 		= Input::get('address_2');
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
	public function destroy($id)
	{
		$addrss = Address::find($id);
		$addrss->delete();

		// redirect
		return Redirect::to('user/address')->with('message', 'Direccion eliminada!');
	}

    /**
     * Devolviendo zonas de la base de datos por una municipality_id
     */
    public function getZonesByMunicipality() {

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
