<?php

class AddressController extends \BaseController {

    /**
     * Display a listing of the resource.
     * GET /address
     *
     * @return Response
     */
    public function index($user_id) {
        $input = Input::all();
        try {
//////////////////////////////////////////////////////

            $lst = array();
            //$zonesLst = Zone::orderBy('zone', 'asc')->get();
            $hoy = date("H:i:s"); 
            log::info('hoy: '.$hoy);
             
            $cierre="22:00:00";
            $apertura="10:00:00";
            if ($hoy>$apertura && $hoy<$cierre ) {
              log::info('SI dentro trabajo '.$hoy);

               $hoy2 = date("w"); 
                $hoy2=$hoy2+1;
                $zonesLst= DB::select('select z.zone, z.zone_id, a.address_id,
                           a.address_name, 
                           a.address_1,
                           a.address_2,
                           a.reference,
                           p.price, 
                           z.shipping_price_id, 
                           t.prom_time, 
                           t.day_id,
                           t.traffic_beginning,
                           t.traffic_end
                    FROM   pf.com_zones z 
                           INNER JOIN pf.com_shipping_prices p 
                                   ON z.shipping_price_id = p.shipping_price_id 
                           LEFT JOIN pf.com_zones_traffic t 
                                  ON z.zone_id = t.zone_id
                            LEFT JOIN diner_addresses a 
                                  ON z.zone_id = a.zone_id 
                    WHERE  z.active = 1 and t.day_id = "'.$hoy2.'" and a.user_id = "'.$user_id.'"
                    and t.traffic_beginning <= "'.$hoy.'" 
                    and t.traffic_end in 
                    (select tc.traffic_end from com_zones_traffic tc where  
                     tc.traffic_end >="'.$hoy.'")  
                     ORDER  BY z.zone');
                    foreach ($zonesLst as $z) {
                        $lst[] = array(
                            'address_name'=>$z->address_name."-Tiempo de entrega estimado: ".($z->prom_time+20)." min",
                            'address_1'=>$z->address_1,
                            'address_2' => $z->address_2,
                            'reference'  => $z->reference,
                            'zone_id'   => $z->zone_id,
                            'address_id'=> $z->address_id,
                            'time'   => ($z->prom_time+20)." min"
                        );
                    }

            } else {
                   log::info('NO dentro trabajo '.$hoy);
            $zonesLst= DB::select('select z.zone, a.address_name, a.address_id, a.address_1, a.address_2, a.reference, z.zone_id, 
                       p.price, 
                       z.shipping_price_id,
                       p.price prom_time
                        FROM   pf.com_zones z INNER JOIN pf.com_shipping_prices p ON z.shipping_price_id = p.shipping_price_id Left join pf.diner_addresses a on z.zone_id = a.zone_id WHERE  z.active = 1  and a.user_id = "'.$user_id.'" 
                 ORDER  BY z.zone'); 
                     foreach ($zonesLst as $z) {
                    $lst[] = array(
                            'address_name'=>$z->address_name."-Tiempo de entrega estimado : Servicio no disponible",
                            'address_1'=>$z->address_1,
                            'address_2' => $z->address_2,
                            'reference'  => $z->reference,
                            'zone_id'   => $z->zone_id,
                            'address_id'=> $z->address_id,
                            'time'   => "Servicio no disponible"
                    );
                 }
            }


/////////////////////////////////////
            $statusCode = 200;
            $limit = Address::where('user_id', $user_id)->count();
            $numDirecciones = $limit;
           
        
            if ($limit > 0) {
                $addrs = Address::where('user_id', $user_id)->get();

                $response['status'] = true;
                $response['data'] = $lst;
            } else {
                $response = array(
                    "status" => false,
                    "data" => 'null'
                );
            }
        } catch (Exception $e) {
            $statusCode = 400;
            $response = array(
                "status" => false,
                "data" => $e->getMessage()
            );
        }
        return Response::json($response, $statusCode);
    }

    /**
     * Show the form for creating a new resource.
     * GET /address/create
     *
     * @return Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     * POST /address
     *
     * @return Response
     */
    public function store() {
        //$input = Input::all();
        $input = $_POST['direccion'];
        try {
            $statusCode = 200;

            $validator = Address::validation($input);
            if ($validator->fails()) {
                $response['status'] = false;
                $response['data'] = $validator->messages();
            } else {
                $addrss = new Address;
                $addrss->user_id = $input['user_id'];
                //$addrss->map_coordinates = Input::get('map_coordinates');
                $addrss->address_name = $input['address_name'];
                $addrss->address_1 = $input['address_1'];
                $addrss->address_2 = $input['address_2'];
                $addrss->city = $input['city'];
                $addrss->state = $input['state'];
                $addrss->reference = $input['reference'];
                $addrss->country_id = 69; // Input::get('country_id');
                $addrss->save();

                $response['status'] = true;
                $response['data'] = array(
                    "address_id" => $addrss->address_id
                );
            }
        } catch (Exception $e) {
            $statusCode = 400;
            $response = array(
                "status" => false,
                "data" => $e->getMessage()
            );
        }
        return Response::json($response, $statusCode);
    }

    /**
     * Store a newly created resource in storage.
     * POST /address
     *
     * @return Response
     */
    public function storeNew($direccion) {
        //$input = Input::all();
        $input = explode(",", $direccion);
        /*$input[0]= user_id
        $input[1]= address_name
        $input[2]= addres_1
        $input[3]= reference
        $input[4]= zone*/

        Log::info($input);
        try {
            $statusCode = 200;

            /*$validator = Address::validation($input);
            if ($validator->fails()) {
                $response['status'] = false;
                $response['data'] = $validator->messages();
            }else{*/
                $addrss = new Address;
                $addrss->user_id = $input[0];
                $addrss->address_name = $input[1];
                $addrss->address_1 = $input[2];
                //$addrss->address_2 = $input['address_2'];
                $addrss->reference = $input[3];
                $addrss->zone_id = $input[4];
                if (isset($input['map_coordinates'])) {
                    $input['map_coordinates']=$input['map_coordinates'];
                    $addrss->map_coordinates = $input['map_coordinates'];
                }else{
                    $input['map_coordinates']= "0.0,0.0";
                    Log::info("No compartio GPS:"."No compartio GPS");
                    $addrss->map_coordinates = $input['map_coordinates'];
                }
               
                //Eliminar Cuando db se actualice
                $addrss->city = "SS";
                $addrss->state = "SS";
                $addrss->country_id = 69;
                $addrss->save();


                /////////////////Enviando en tiempo para actualizar la vista
                if (isset($input['Android_add'] ) && $input['Android_add']=='Android') {      

                    $lst = array();
                    //$zonesLst = Zone::orderBy('zone', 'asc')->get();
                    $hoy = date("H:i:s"); 
                    log::info('hoy: '.$hoy);
                     
                    $cierre="22:00:00";
                    $apertura="10:00:00";
                    if ($hoy>$apertura && $hoy<$cierre ) {
                        log::info('Agregando Direccion mostrando timepo '.$hoy);

                        $hoy2 = date("w"); 
                        $hoy2=$hoy2+1;
                        $zonesLst= DB::select('select z.zone, z.zone_id, 
                            p.price, 
                            t.prom_time, 
                            t.day_id,
                            t.traffic_beginning,
                            t.traffic_end
                            FROM   pf.com_zones z 
                            INNER JOIN pf.com_shipping_prices p 
                                ON z.shipping_price_id = p.shipping_price_id 
                            LEFT JOIN pf.com_zones_traffic t 
                                ON z.zone_id = t.zone_id
                            
                            WHERE  z.active = 1 and z.zone_id = "'.$input['zone_id'].'" and t.day_id = "'.$hoy2.'" 
                            and t.traffic_beginning <= "'.$hoy.'" 
                            and t.traffic_end in 
                            (select tc.traffic_end from com_zones_traffic tc where  
                            tc.traffic_end >= "'.$hoy.'" )  
                            GROUP BY z.zone_id ORDER  BY z.zone');

                        foreach ($zonesLst as $z) {
                            $lst[] = array(
                                'zone_id'   => $z->zone_id,
                                'zone'   => $z->zone,
                                'price'   => $z->price,
                                'time'   => ($z->prom_time+20)." min"
                            );
                        }
                    }else{
                        log::info('NO dentro trabajo '.$hoy);
                        $zonesLst= DB::select('select z.zone, z.zone_id, 
                            p.price, 
                            p.price prom_time
                            FROM   pf.com_zones z INNER JOIN pf.com_shipping_prices p ON z.shipping_price_id = p.shipping_price_id Left join pf.diner_addresses a on z.zone_id = a.zone_id WHERE 
                            z.active = 1 and z.zone_id = "'.$input['zone_id'].'" 
                            GROUP BY z.zone_id ORDER  BY z.zone'); 
                            foreach ($zonesLst as $z) {
                                $lst[] = array(
                                    'zone_id'   => $z->zone_id,
                                    'zone'   => $z->zone,
                                    'price'   => $z->price,
                                    'time'   => "Servicio no disponible"
                                );
                            }
                    }

                    $response['status'] = true;
                    $response['data'] = array(
                        "address_id" => $addrss->address_id,
                        "time"=> $addrss->address_id,
                    );
                    /////////////////////////////////////

                    /*$zonesLst = Zone::orderBy('zone', 'asc')->get();

                    foreach ($zonesLst as $z) {
                        $lst[] = array(
                            'zone_id' => $z->zone_id,
                            'zone' => $z->zone
                        );
                    }*/
                }else {//Mostrar direccion agregada si no es android nuevo o si es iOS
                    $response['status'] = true;
                    $response['data'] = array(
                            "address_id" => $addrss->address_id
                        );                                    
                }            
            //}
        }catch (Exception $e) {
            $statusCode = 400;
            $response = array(
                "status" => false,
                "data" => $e->getMessage()
            );
        }
        return Response::json($response, $statusCode);
    }

    /**
     * Display the specified resource.
     * GET /address/{id}
     *
     * @param  int  $id
     * @return Response
     */
    public function show($address_id) {
        $input = Input::all();
        try {
            $statusCode = 200;
            $addr = Address::findOrFail($address_id);
            $zone=Zone::findOrFail($addr->zone_id);
            $municipality = Municipality::findOrFail($zone->municipality_id);
            $state = State::findOrFail($municipality->state_id);

            $addr->municipality_id = $municipality->municipality_id;
            $addr->municipality=$municipality->municipality;
            $addr->state_id = $municipality->state_id;
            $addr->state = $state->state;
            $addr->zone=$zone->zone;

            $response['status'] = true;
            $response['data'] = $addr;
        } catch (Exception $e) {
            $statusCode = 400;
            $response = array(
                "status" => false,
                "data" => $e->getMessage()
            );
        }
        return Response::json($response, $statusCode);
    }

    /**
     * Show the form for editing the specified resource.
     * GET /address/{id}/edit
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     * PUT /address/{id}
     *
     * @param  int  $id
     * @return Response
     */
    public function update() {
        $input = Input::all();
        try {
            $statusCode = 200;
            $validator = Address::validation($input);

            if ($validator->fails()) {
                $response['status'] = false;
                $response['data'] = $validator->messages();
            } else {
                $addrss = Address::findOrFail($input['address_id']);
                //$addrss->user_id  = Input::get('user_id');//Auth::user()->user_id;
                //$addrss->map_coordinates = Input::get('map_coordinates');
                $addrss->address_name = $input['address_name'];
                $addrss->address_1 = $input['address_1'];
                $addrss->address_2 = $input['address_2'];
                $addrss->zone_id    = Input::get('zone_id');
                $addrss->reference = $input['reference'];
                // $addrss->country_id  = Input::get('country_id');
                if (isset($input['map_coordinates'])) {
                   $addrss->map_coordinates = $input['map_coordinates'];
                } else {
                   $addrss->map_coordinates ="0.0,0.0";
                }
                
                $addrss->save();

                $response['status'] = true;
                $response['data'] = array(
                    "address_id" => $addrss->address_id
                );
            }
        } catch (Exception $e) {
            $statusCode = 400;
            $response = array(
                "status" => false,
                "data" => $e->getMessage()
            );
        }
        return Response::json($response, $statusCode);
    }

    /**
     * Remove the specified resource from storage.
     * DELETE /address/{id}
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy() {
        $input = Input::all();
        try {
            $statusCode = 200;
            $addrss = Address::findOrFail($input['address_id']);
            $addrss->delete();

            $response = array(
                "status" => True,
                "data" => "Direccion eliminada."
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

    /**
     * Devolviendo todas las zonas de la base de datos
     */
    public function getZones() {
        $statusCode = 200;
        try {
            $lst = array();
            $zonesLst = Zone::orderBy('zone', 'asc')->get();

            foreach ($zonesLst as $z) {
                $lst[] = array(
                    'zone_id' => $z->zone_id,
                    'zone' => $z->zone
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


     /**
     * Devolviendo todas las zonas de la base de datos con su costo de envio
     */
    public function getZonesShippingPrice() {
        $statusCode = 200;
        try {
            $lst = array();
            //$zonesLst = Zone::orderBy('zone', 'asc')->get();
            $hoy = date("H:i:s"); 
            log::info('hoy: '.$hoy);

            $cierre="22:00:00";
            $apertura="10:00:00";
            if ($hoy>$apertura && $hoy<$cierre ) {
               log::info('SI dentro trabajo '.$hoy);

               $hoy2 = date("w"); 
                $hoy2=$hoy2+1;
                $zonesLst= DB::select('select z.zone, 
                           p.price, 
                           z.shipping_price_id, 
                           t.prom_time, 
                           t.day_id,
                           t.traffic_beginning,
                           t.traffic_end
                        FROM   pf.com_zones z 
                            INNER JOIN pf.com_shipping_prices p 
                                ON z.shipping_price_id = p.shipping_price_id 
                            LEFT JOIN pf.com_zones_traffic t 
                                ON z.zone_id = t.zone_id 
                    WHERE  z.active = 1 and t.day_id = "'.$hoy2.'" 
                    and t.traffic_beginning <= "'.$hoy.'" 
                    and t.traffic_end in 
                    (select tc.traffic_end from com_zones_traffic tc where  
                    tc.traffic_end >="'.$hoy.'")  
                    ORDER  BY z.zone');
                    foreach ($zonesLst as $z) {
                        $lst[] = array(
                            'Nombre' => $z->zone,
                            'Costo'  => $z->price,
                            'zona'   => $z->shipping_price_id,
                            'time'   => "A esta hora: ".($z->prom_time+20)." min"
                        );
                    }


            } else {
                    log::info('NO dentro trabajo '.$hoy);
            $zonesLst= DB::select('select z.zone, 
                       p.price, 
                       z.shipping_price_id,
                       p.price prom_time
                        FROM   pf.com_zones z INNER JOIN pf.com_shipping_prices p ON z.shipping_price_id = p.shipping_price_id WHERE  z.active = 1  
                 ORDER  BY z.zone'); 
                     foreach ($zonesLst as $z) {
                    $lst[] = array(
                        'Nombre' => $z->zone,
                        'Costo'  => $z->price,
                        'zona'   => $z->shipping_price_id,
                        'time'   => $z->prom_time="Servicio no disponible"
                    );
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
                    'zone' => $z->zone
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

            $lst = State::where('country_id', $input['country_id'])->where('active', true)
                    ->get();

            for ($i=0; $i<count($lst); $i++) {
                $lst[$i]->municipalities = Municipality::where('state_id', $lst[$i]->state_id)
                        ->where('active', true)->get();

                for ($j=0; $j<count($lst[$i]->municipalities); $j++) {
                    $lst[$i]->municipalities[$j]->zones=Zone::where('municipality_id', $lst[$i]->municipalities[$j]->municipality_id)
                    ->where('active', 1)->get();
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

    /**
    * Metodo que actualiza las coordenadas de una direccion de usuario
    * POST /address/update-coordinates
    * @param  int  $address_id
    * @param  string  $map_coordinates
    * @return Response
    */
    public function update_coordinates() {
        $input = Input::all();
        try {
            $statusCode = 200;

            $address = Address::findOrFail($input['address_id']);

            $address->map_coordinates = $input['map_coordinates'];
            $address->save();

            $response['status'] = true;
            $response['data'] = "Coordenadas almacenadas correctamente";

        } catch (Exception $e) {
            $statusCode = 400;
            $response = array(
                "status" => false,
                "data" => $e->getMessage()
            );
        }
        return Response::json($response, $statusCode);
    }

    public function zones(){
        
       try{
            $statusCode = 200;
            $zonas = DB::table('com_zones')->orderBy('zone_id', 'asc')->get();

            $response['status'] = true;
            $response['data'] = $zonas;
        }catch (Exception $e){
            $statusCode = 400;
            $response = array(
                "status" => false,
                "data" => $e->getMessage()
            );
        }

        return Response::json($response, $statusCode);
    }

}
