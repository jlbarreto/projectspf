<?php

class BinController extends \BaseController {

    public function verify() {
        $input = Input::all();
        try {
            $statusCode = 200;
            $activate=0;
            $porcentaje=0;
            $bin = $input['bin']; //Capturamos el bin enviado por el usuario
            //verificamos el bin
            $verificateBin = ListBins::where('num_bin',$bin)->count();
            if ($verificateBin>=1) {
              //si el bin está en la lista buscamos la configuración de descuento activa
              $config = ConfigBins::where('activate',1)->orderBy('id_config','desc')->first();
              if (empty($config)==false) {
                $porcentaje = $config->porcentaje;
                $activate   = 1; //activamos el descuento
              }
            }
            $response = array(
                "status" => true,
                "active" =>$activate,
                "porcentaje" =>$porcentaje
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
