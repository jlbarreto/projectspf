<?php
class MensajeroStatusLogController extends \BaseController {

	/**
     * Display a listing of the resource.
     * GET /getGeneralOptions
     *
     * @return Response
     */
	 public function getMensajeroStatusLog( )
	{
		 $input = Input::all();
	    
	    try {
	      $statusCode = 200;
	     // $input['motorista_id']=1;
	      $MensajeroStatusLog = MensajeroStatusLog::where('motorista_id','=',$input['motorista_id'])
	      ->orderBy('mensajero_status_id','desc')
	      ->get();
	      
	      if (!empty($MensajeroStatusLog)) {
	        $response['status'] = true;
	        $response['data'] = $MensajeroStatusLog;
	      } else {
	        $response = array(
	          "status" => false,
	          "data" => 'Error en los estados del MensajeroStatusLogController'
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
    * Metodo que actualiza las coordenadas de una direccion de usuario
    * POST /address/update-coordinates
    * @param  int  $address_id
    * @param  string  $map_coordinates
    * @return Response
    */
    public function updateMensajeroStatusLog() {
        $input = Input::all();
        try {
            $statusCode = 200;
        
            $MensajeroStatusLog = MensajeroStatusLog::where('mensajero_status_id', '=', $input['mensajero_status_id'])->where('motorista_id', '=', $input['motorista_id'])->orderBy('created_at','desc')->firstOrFail();
            Log::info('Coordenadas: '.$input['mensajero_coordenadas']);
            $MensajeroStatusLog->mensajero_coordenadas = $input['mensajero_coordenadas'];
            $MensajeroStatusLog->save();

            $response['status'] = true;
            $response['data'] = "Coordenadas actulizadas correctamente";

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
    public function storeMensajeroStatusLog() {
        $input = Input::all();
        try {
            $statusCode = 200;

            
            	//$input['motorista_id']=1;
            	//$input['mensajero_status_id']=3;
            	//$input['mensajero_coordenadas']='9898.909';
                if (isset($input['mensajero_comment'])) {
                $input['mensajero_comment'] = $input['mensajero_comment'];
                } else {
                $input['mensajero_comment'] = "";
                }
                
                $MensajeroStatusLog = new MensajeroStatusLog;
                $MensajeroStatusLog->mensajero_status_id = $input['mensajero_status_id'];
                $MensajeroStatusLog->mensajero_coordenadas = $input['mensajero_coordenadas'];
                $MensajeroStatusLog->mensajero_comment = $input['mensajero_comment'];
                $MensajeroStatusLog->motorista_id = $input['motorista_id'];
                $MensajeroStatusLog->order_id = $input['order_id'];
                $MensajeroStatusLog->restaurant_id = $input['restaurant_id'];
                $MensajeroStatusLog->save();

                $response['status'] = true;
                $response['data'] = array(
                    "motorista_id" => $MensajeroStatusLog->motorista_id
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