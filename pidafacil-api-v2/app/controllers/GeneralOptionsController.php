<?php
class GeneralOptionsController extends \BaseController {

	/**
     * Display a listing of the resource.
     * GET /getGeneralOptions
     *
     * @return Response
     ****/
	/*public function getGeneralOptions( ){
		// Show general options pf
	    
	    try {
	      	$statusCode = 200;

	      	$pfOptions = GeneralOptions::findOrFail(1);
       		log::info("NumeroWhatsapp:  ".$pfOptions->num_atencion_cliente);
      		if (!empty($pfOptions)) {
        		$response['status'] = true;
        		$response['data'] = $pfOptions;
      		}else{
        		$response = array(
          			"status" => false,
          			"data" => 'No hay opciones generales en la BD.'
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
	}*/

	public function getGeneralOptions(){
		$pfOptions = GeneralOptions::findOrFail(1);
		Log::info($pfOptions);
		return json_encode($pfOptions);
	}
}