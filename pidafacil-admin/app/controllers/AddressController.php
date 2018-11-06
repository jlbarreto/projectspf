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
		$addrss = Address::where('user_id', $user_id)->get();
		
		return View::make('web.user_directions')->with('addresses', $addrss);
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
			$response['status'] = false;
			$response['data']	= $validator->messages();

			return $response;
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
			/*
			$response['status'] = true;
			$response['data']	= $addrss;
			*/
			$addrs = Address::where('user_id', Auth::user()->user_id)->get();

			return Response::json($addrs, 200);
		}
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
		//
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
			return Redirect::to('user/address')
            	->withErrors($validator)
            	->withInput();
		}else{ 
			$addrss = Address::find(Input::get('address_id'));
			//$addrss->user_id	= Input::get('user_id');//Auth::user()->user_id;
			//$addrss->map_coordinates = Input::get('map_coordinates'); 
			$addrss->address_name 	= Input::get('address_name');
			$addrss->address_1 		= Input::get('address_1');
			$addrss->address_2 		= Input::get('address_2');
			$addrss->city 			= Input::get('city');
			$addrss->state 			= Input::get('state');
			$addrss->reference 			= Input::get('reference');
			// $addrss->country_id 	= Input::get('country_id');
			$addrss->save();
			
			$response['status'] = true;
			$response['data']	= $addrss;
			
			return Redirect::to('user/address')->with('message', 'Direccion actualizada!');
		}
		return Redirect::to('user/address');
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

}
