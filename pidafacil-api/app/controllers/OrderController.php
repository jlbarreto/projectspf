<?php

class OrderController extends \BaseController {

    /**
     * Display a listing of the resource.
     * GET /order
     *
     * @return Response
     */
    public function index() {
        try {
            $statusCode = 200;
            $input = Input::all();
            $user_id = $input['user_id'];

            if (isset($input['page_size']) && $input['page_size'] > 0) {
                $pagesze = $input['page_size'];
                $pagepos = $input['page_post'];
                $orders = Order::join('req_order_ratings', function($join) use($user_id) {
                            $join->on('req_orders.order_id', '=', 'req_order_ratings.order_id')
                            ->where('req_order_ratings.user_id', '=', $user_id);
                        })->leftJoin('res_restaurants', 'req_orders.restaurant_id', '=', 'res_restaurants.restaurant_id')
                        ->select('req_orders.order_id',
                                 'req_orders.order_cod',
                                 'res_restaurants.name',
                                 'req_orders.order_total',
                                 'req_orders.pay_bill',
                                 'req_orders.shipping_charge',
                                 'req_orders.credit_charge',
                                 'req_orders.tigo_money_charge',
                                 'req_orders.created_at')
                        ->orderBy('created_at', 'desc')
                        ->groupBy('req_order_ratings.order_id')
                        ->take($pagesze)->skip($pagepos * $pagesze)
                        ->get();
            } else {
                $orders = Order::join('req_order_ratings', function($join) use($user_id) {
                            $join->on('req_orders.order_id', '=', 'req_order_ratings.order_id')
                            ->where('req_order_ratings.user_id', '=', $user_id);
                        })->leftJoin('res_restaurants', 'req_orders.restaurant_id', '=', 'res_restaurants.restaurant_id')
                        ->select('req_orders.order_id',
                                 'req_orders.order_cod',
                                 'res_restaurants.name',
                                 'req_orders.order_total',
                                 'req_orders.pay_bill',
                                 'req_orders.shipping_charge',
                                 'req_orders.credit_charge',
                                 'req_orders.tigo_money_charge',
                                 'req_orders.created_at')
                        ->orderBy('created_at', 'desc')
                        ->groupBy('req_order_ratings.order_id')
                        ->get();
            }

            foreach ($orders as $key => $val) {
                $status_log = OrderStatusLog::where('order_id', $val->order_id)
                                ->with('orderStatus')->orderBy('order_status_id', 'desc')->first();

                  if ($status_log->order_status->order_status=='Pendiente validación Tigo Money') {
                           $status_log = OrderStatusLog::where('order_id', $val->order_id)
                           ->with('orderStatus')->orderBy('created_at', 'desc')->first();


                } else {
                    $status_log = OrderStatusLog::where('order_id', $val->order_id)
                                ->with('orderStatus')->orderBy('created_at', 'desc')->first();
                }

               // Log::info('Tigo money: '.$val->tigo_money_charge);
                $total = (substr($val->order_total, 1) + $val->shipping_charge + $val->credit_charge + $val->tigo_money_charge);
                $val->order_total = number_format($total,2,",",".");
                $val->status_logs = array(
                    'order_status' => $status_log->order_status->order_status,
                    'created_at' => date('d/m/Y h:ia', strtotime($status_log->created_at))
                );
                $order[] = $val;
            }

            if (isset($order) && count($order) > 0) {
                $response = array(
                    "status" => true,
                    "data" => $order
                );
            } else {
                $response = array(
                    "status" => false,
                    "data" => "No hay ordenes disponibles"
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
     * GET /order/create
     *
     * @return Response
     */
    public function create() {
        //
    }

    //Genera numero de orden de manera aleatoria
    public function numeroOrden($length = 0, $uc = FALSE, $n = TRUE, $sc = FALSE) {
        //se consulta la Hora y se le agrega al final para evitar que se repita el numero
        $hora = date("G");
        //se compara la cantidad de digitos para colocar el numero de digitos en length
        if (strlen($hora) == 2) {
            $length = 4;
        } else {
            $length = 5;
        }
        $source = 'ABCDEFGHJKMNPQRSTUVWXYZ';
        if ($uc == 1)
            $source .= 'abcdefghijklmnopqrstuvwxyz';
        if ($n == 1)
            $source .= '23456789';
        if ($sc == 1)
            $source .= '|@#~$%()=^*+[]{}-_';
        if ($length > 0) {
            $rstr = "";
            $source = str_split($source, 1);
            for ($i = 1; $i <= $length; $i++) {
                mt_srand((double) microtime() * 1000000);
                $num = mt_rand(1, count($source));
                $rstr .= $source[$num - 1];
            }
        }
        $rstr = $rstr . $hora;

        return $rstr;
    }

    /**
     * Store a newly created resource in storage.
     * POST /order
     *
     * @return Response
     */

    /*

     $input['products'] = (object)array(array('product_id' => 6886,'quantity' => 2)
                ,array('product_id' => 6887,'quantity' => 2),array('product_id' => 8051,'quantity' => 1),array('product_id' => 8053,'quantity' => 2));
    */
    public function store() {

            $config = DB::table('config_bins')->get();
            $porcentaje = $config[0]->porcentaje;
            $activate = $config[0]->activate;
            $input = Input::all();
            try {
                $statusCode = 200;
                $user_id = $input['user_id'];
                $order = new Order;
                $order->customer = (isset($input['customer']) ? $input['customer'] : '');
                $order->customer_phone = (isset($input['customer_phone']) ? $input['customer_phone'] : '');
                $order->restaurant_id = $input['restaurant_id'];

                //Seter de los primeros productos
                $total = 0;

                foreach ($input['products'] as $product) {
                    $prd = Product::findOrFail($product['product_id']);
                    $total = $total + ($prd->value*$product['quantity']);
                }
                if ($activate == 1  && $input['payment_method_id'] == 2) {
                    $descuentoMonto = round(($total * $porcentaje/100),2);
                    $total = round(($total - $descuentoMonto),2);

                }
                $order->order_total = $total;
                $order->service_type_id = $input['service_type_id'];

                //Si es cualquiera de los dos servicios a domicilio
                if ($order->service_type_id == 1 || $order->service_type_id == 3) {
                    if(!Restaurant::isOpen($order->restaurant_id, $order->service_type_id))
                        throw new Exception("Lo sentimos, en este momento el restaurante se encuentra fuera de nuestro horario de servicio. Le solicitamos verificar los horarios en la secciÃ³n InformaciÃ³n del Restaurante.");
                    $addresses = Address::where('user_id', $user_id)->get();

                    //si existe solo una direcciÃ³n se trabajarÃ¡ con ella
                    // Si esxiste mÃ¡s de una direcciÃ³n se espera parÃ¡metro para decidir con cual trabajarÃ¡
                    if (count($addresses) == 1) {
                        $direccion = $addresses[0];
                    } else {
                        $direccion = Address::where('address_id', $input['address_id'])->first();
                    }

                    $addr = $direccion->address_name . ' - ' . $direccion->address_1 .
                                (!empty($direccion->address_2) ? ', ' . $direccion->address_2 : '');
                    $order->address = $addr;
                    $order->address_id=$direccion->address_id;

                    //Definiendo costo de envÃ­o
                    if ($order->service_type_id == 1) {
                        $restaurant = Restaurant::findOrFail($order->restaurant_id);
                        $order->shipping_charge = $restaurant->shipping_cost;

                        if ($activate == 1  && $input['payment_method_id'] == 2) {

                            $descuentoMonto = round(($restaurant->shipping_cost * $porcentaje/100),2);
                            $order->shipping_charge = round(($restaurant->shipping_cost - $descuentoMonto),2);
                        }

                    } else {
                        $zone = DB::table('restaurants_zones')->where('zone_id', $direccion->zone_id)->where('restaurant_id', $order->restaurant_id)->first();

                    /***************CALCULO EL FREE SHIPPING SI APLICA****************/
                        $free = DB::SELECT('
                                SELECT * FROM pf.free_shipping_restaurants
                                WHERE restaurant_id = "'.$order->restaurant_id.'"
                            ');

                        $total = $order->order_total;
                        $totalF = str_replace("$","",$total);

                        Log::info('Total controller2: '.$totalF);
                        $contArr = count($free);

                        if($contArr > 0) {
                            foreach ($free as $value) {
                                $arreglo = $value;
                            }

                            Log::info("Monto minimo2: ".$arreglo->monto_minimo);

                            $conteo = count($arreglo);

                            if($conteo > 0 && $totalF >= $arreglo->monto_minimo){
                                Log::info("entra al if conteo");
                                $order->shipping_charge = 0.0;
                            }else{
                                Log::info("NO entra al if conteo");

                                if($zone==NULL){
                                    $order->shipping_charge = 0;
                                }
                                else if ($activate == 1 && $input['payment_method_id'] == 2) {

                                    $descuentoMonto = round(($zone->shipping_charge * $porcentaje/100),2);
                                    $order->shipping_charge = round(($zone->shipping_charge - $descuentoMonto),2);
                                }else {
                                    $order->shipping_charge = $zone->shipping_charge;;
                                }
                            }
                        }else{
                            Log::info("NO entra al if conteo");
                            if($zone==NULL){
                                $order->shipping_charge = 0;
                            }
                            else if ($activate == 1 && $input['payment_method_id'] == 2) {

                                $descuentoMonto = round(($zone->shipping_charge * $porcentaje/100),2);
                                $order->shipping_charge = round(($zone->shipping_charge - $descuentoMonto),2);
                            }else {
                                $order->shipping_charge = $zone->shipping_charge;;
                            }
                        }

                        /*************FIN FREE DELIVERY*************/

                    }

                //Si es para llevar
                } else {
                    $order->restaurant_id = $input['res_address_id'];
                    $order->pickup_hour = $input['pickup_hour'];
                    $order->pickup_min = $input['pickup_min'];
                    $order->shipping_charge = 0;
                    if(!Restaurant::isOpen($order->restaurant_id,$order->service_type_id,$order->pickup_hour.':'.$order->pickup_min.':00')){
                        throw new Exception("Lo sentimos, en este momento el restaurante se encuentra fuera de nuestro horario de servicio. Le solicitamos verificar los horarios en la secciÃ³n InformaciÃ³n del Restaurante.");
                    }
                    $direccion = Restaurant::where('restaurant_id', $order->restaurant_id)->first();
                    $addr = $direccion->name . ' - ' . $direccion->address;
                    $order->address = $addr;
                }

                ///Guardando Origen del Dispositivo
                if (isset($input['source_device'])) {
                    $order->source_device=$input['source_device'];
                } else {
                    $order->source_device='Android_olds';
                    $input['source_device']='Android_olds';
                }
                $usuario = User::where('user_id', $user_id)->get();
                $datosUsuario = json_decode($usuario, true);


                //Manejo de Ordenes iOS y androids olds
                $amex= substr($input['credit_card'], 0, 2);
                log::info('amex: '.$amex);
                $payment_method = PaymentMethod::findOrFail($input['payment_method_id']);
                $order->payment_method_id = $payment_method->payment_method_id;
                if ($payment_method->payment_method_id == 2 && $datosUsuario[0]['sospecha']!=1 && ($amex =='37'||$amex=='34') && ($input['source_device']=='Android' || $input['source_device']=='iOS')) {
                    $order->credit_name = $input['credit_name'];
                    $order->credit_card = $input['credit_card'];
                    $order->credit_expmonth = $input['credit_expmonth'];
                    $order->credit_expyear = $input['credit_expyear'];
                    $order->secure_code = $input['secure_code'];
                    $card = DB::table('res_payment_methods')->where('payment_method_id', 2)->first();
                    $subtotal = substr($order->order_total,1);
                    $credit_charge = round((($subtotal + $order->shipping_charge) * $card->payment_method_charge),6);
                    $tarjetaNoEncript=1;
                    $credit_charge= $credit_charge+0;
                    $cad = explode(".", $credit_charge);
                    $rest= substr($cad[1], 2, 1);
                    if ($rest!=null && $rest<5 && $rest!=0) {
                        $credit_charge=round(($credit_charge+0.01),2);;
                    } else {
                        $credit_charge=round(($credit_charge),2);
                    }
                    $order->order_cod = $this->numeroOrden();
                    $order->credit_charge = $credit_charge;
                    $order->save();

                    if ($activate == 1) {
                        $descuentoMonto = round(($credit_charge * $porcentaje/100),2);
                        $credit_charge = round(($credit_charge - $descuentoMonto),2) +0.02;
                        $order->credit_charge =$credit_charge;
                        $order->save();

                    }

                    $order_stat = new OrderStatusLog;
                    $order_stat->order_id = $order->order_id;
                    $order_stat->user_id = $user_id;
                    $order_stat->order_status_id = 2;
                    $order_stat->save();
                }
                if ($payment_method->payment_method_id == 2 && $datosUsuario[0]['sospecha']!=1 && $input['source_device']=='Android_olds') {
                    $order->credit_name = $input['credit_name'];
                    $order->credit_card = $input['credit_card'];
                    $order->credit_expmonth = $input['credit_expmonth'];
                    $order->credit_expyear = $input['credit_expyear'];
                    $order->secure_code = $input['secure_code'];
                    $card = DB::table('res_payment_methods')->where('payment_method_id', 2)->first();
                    $subtotal = substr($order->order_total,1);
                    $credit_charge = round((($subtotal + $order->shipping_charge) * $card->payment_method_charge),6);
                    $credit_charge= $credit_charge+0;
                    $cad = explode(".", $credit_charge);
                    $rest= substr($cad[1], 2, 1);
                    if ($rest!=null && $rest<5 && $rest!=0) {
                       $credit_charge=round(($credit_charge+0.01),2);
                    } else {
                        $credit_charge=round(($credit_charge),2);
                    }

                    if ($activate == 1) {
                        $descuentoMonto = round(($credit_charge * $porcentaje/100),2);
                        $credit_charge = round(($credit_charge - $descuentoMonto),2) + 0.02;

                    }
                    $order->order_cod = $this->numeroOrden();
                    $order->credit_charge = $credit_charge;
                    $order->save();
                    $order_stat = new OrderStatusLog;
                    $order_stat->order_id = $order->order_id;
                    $order_stat->user_id = $user_id;
                    $order_stat->order_status_id = 2;
                    $order_stat->save();

                } elseif ($payment_method->payment_method_id == 1) {
                    ///////////////Efectivo
                    $order->pay_bill = $input['pay_bill'];
                    $pago = round(($order->pay_bill),2);
                    $totalOrden =round((substr($order->order_total, 1)),2);
                    $costoEnvio = round($order->shipping_charge,2);
                    $totalCliente = ($totalOrden + $costoEnvio);
                    $cambioCliente = $pago - $totalCliente;
                    if (round($totalCliente,2) == round($pago,2) ) {
                        $order->pay_change = 0;
                    } else {
                        $order->pay_change = $pago - $totalCliente;
                    }
                    if($order->pay_change < 0)
                        throw new Exception("Debe ingresar una cantidad igual o mayor a la del precio total");
                }elseif ($payment_method->payment_method_id == 3){
                    ///////////////Tigo Money
                    $subtotal = substr($order->order_total,1);
                    $order->billetera_user = $input['billetera_user'];
                    $order->num_tigo_money = $input['num_tigo_money'];
                    $tigo = DB::table('res_payment_methods')->where('payment_method_id', 3)->first();
                    $tigo_money_charge=round((($subtotal + $order->shipping_charge) * $tigo->payment_method_charge),6);
                    //aproximando  0.17575 a 0.18
                    $tigo_money_charge= $tigo_money_charge+0;
                    if($tigo_money_charge != 0){
                        $cad = explode(".", $tigo_money_charge);
                        $rest= substr($cad[1], 2, 1);
                        if ($rest<5 && $rest!=0 && $rest!=null) {
                           $tigo_money_charge=round(($tigo_money_charge+0.01),2);
                        } else {
                            $tigo_money_charge=round(($tigo_money_charge),2);
                        }
                    }else{
                        $tigo_money_charge = 0;
                    }
                    $order->tigo_money_charge = $tigo_money_charge;
                }
                $order->order_cod = $this->numeroOrden();
                $usuario = User::where('user_id', $user_id)->get();
                $datosUsuario = json_decode($usuario, true);


                ///Manejo Ordenes Android
                if (isset($tarjetaNoEncript)) {
                    $tarjetaNoEncript=1;
                } else {
                    $tarjetaNoEncript=0;
                }

                ////////////////////banca
                if ($payment_method->payment_method_id == 2 && $datosUsuario[0]['sospecha']!=1 && ($input['source_device']=='Android' || $input['source_device']=='iOS') && $tarjetaNoEncript!=1) {

                    $order->credit_name = $input['credit_name'];
                    $order->credit_card = $input['credit_card'];
                    $order->credit_expmonth = $input['credit_expmonth'];
                    $order->credit_expyear = $input['credit_expyear'];
                    $order->secure_code = $input['secure_code'];
                    $card = DB::table('res_payment_methods')->where('payment_method_id', 2)->first();
                    $subtotal = substr($order->order_total,1);
                    $credit_charge = round((($subtotal + $order->shipping_charge) * $card->payment_method_charge),6);
                    $credit_charge= $credit_charge+0;
                    $cad = explode(".", $credit_charge);
                    $rest= substr($cad[1], 2, 1);
                    if ($rest!=null && $rest<5 && $rest!=0) {
                        $credit_charge=round(($credit_charge+0.01),2);
                    } else {
                        $credit_charge=round(($credit_charge),2);
                    }
                    if ($activate == 1) {
                        $descuentoMonto = round(($credit_charge * $porcentaje/100),2);
                        $credit_charge = round(($credit_charge - $descuentoMonto),2) + 0.02;

                    }

                    $order->credit_charge = $credit_charge;
                    //Obteniendo los parametros del banco desde la tabla pf.bank_option
                    $bank_option = BankOption::findOrFail(1);
                    $password = $bank_option->username_password;
                    $username = $bank_option->username_bank;
                    $type = $bank_option->type_bank;
                    $key_BAC = $bank_option->key_bank;//Key privado de la cuenta bancaria,
                    $key_ID_BAC = $bank_option->key_id_bank;// Key publica
                    $UnixTime = time();
                    $amount = round((floatval(substr($order->order_total, 1))),2) + round(($order->shipping_charge),2)+$order->credit_charge +0.01;
                    $ccnumber = $order->credit_card = $input['credit_card']; //dummy visa
                    $ccexp = $order->credit_expmonth.substr($order->credit_expyear,-2);// contruyendo mes/aÃ±o con 2 digitos
                    $cvv = $order->secure_code = $input['secure_code'];
                    $bank_url = $bank_option->url_post_bank;
                    $amount =number_format((float)$amount, 2, '.', '');

                    Log::info('Tarjeta encriptada: '.$order->credit_card);
                    Log::info('Total de la compra: '.$amount);
                    Log::info('Tiempo Unix '.$UnixTime);
                    $today = date("Y-m-d\ H:i:s",$UnixTime);
                    log::info('Tiempo de envÃ­o: '. $today);
                    Log::info('Usuario de la BD: '.$username);
                    Log::info('password BD: '.$password);
                    Log::info('NÃºmero de Orden: '.$order->order_cod);
                    Log::info('llave del banco: '.$bank_option->key_bank);

                    // abrimos la sesiÃ³n cURL
                    $ch = curl_init();

                    $timeout = 30; // set to zero for no timeout
                    $data = "username=".$username."&"."password=".$password."&"."type=".$type."&"."amount=".$amount."&"."encrypted_payment=".$order->credit_card;

                    Log::info('Parametros enviados: '.$data);
                    // definimos la URL a la que hacemos la peticiÃ³n
                    curl_setopt($ch, CURLOPT_URL,$bank_url);
                    // definimos el nÃºmero de campos o parÃ¡metros que enviamos mediante POST
                    curl_setopt($ch, CURLOPT_POST, 1);
                    // definimos cada uno de los parÃ¡metros
                    curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
                    // recibimos la respuesta y la guardamos en una variable
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    //tiempos de espera de conexion
                    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
                    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
                    $remote_server_output = curl_exec ($ch);
                    // cerramos la sesiÃ³n cURL
                    curl_close ($ch);
                    // $respuestaBAC = explode("&", $remote_server_output);

                    /* Vector de la respuesta Bac */
                    $response = explode('&',$remote_server_output);
                    $respuestaBAC = array();
                    foreach($response AS $key => $value){
                        $newValues = explode('=',$value);
                        $respuestaBAC[$newValues[0]] = $newValues[1];
                    }

                    Log::info('respuestaBAC: '.$remote_server_output);
                    Log::info('transactionid: '.$respuestaBAC['transactionid']);
                    Log::info('response_code: '.$respuestaBAC['response_code']);
                    Log::info('responsetext: '.$respuestaBAC['responsetext']);

                    //$respuestaBAC['response_code']=;
                    switch ($respuestaBAC['response_code']) {
                        case 100:
                        # Transaccion acceptada...
                            $order->credit_card='Encriptada';
                            $order->transaction_id = $respuestaBAC['transactionid'];
                            $order->save();
                            $order_stat = new OrderStatusLog;
                            $order_stat->order_id = $order->order_id;
                            $order_stat->user_id = $user_id;
                            $order_stat->order_status_id = 2;
                            $order_stat->save();
                            break;
                        case 200:
                        # Orden denegada
                            throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro mÃ©todo de pago.");
                            break;
                        case 201:
                            throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro mÃ©todo de pago.");
                            break;
                        case 202:
                            throw new Exception("Lo sentimos, Fondos insuficientes. Intente con otro mÃ©todo de pago.");
                            break;
                        case 203:
                            throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro mÃ©todo de pago.");
                            break;
                        case 204:
                            throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro mÃ©todo de pago.");
                            break;
                        case 220:
                            throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro mÃ©todo de pago.");
                            break;
                        case 221:
                            throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro mÃ©todo de pago.");
                            break;
                        case 222:
                            throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro mÃ©todo de pago.");
                            break;
                        case 223:
                            throw new Exception("Lo sentimos, Su tarjeta esta expirada. Intente con otro mÃ©todo de pago.");
                            break;
                        case 224:
                            throw new Exception("Fecha de vencimiento invÃ¡lida, verifique sus datos");
                            break;
                        case 225:
                            throw new Exception("CÃ³digo de serguridad invÃ¡lido, verifique sus datos");
                            break;
                        case 240:
                            throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro mÃ©todo de pago.");
                            break;
                        case 250:
                            throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro mÃ©todo de pago.");
                            break;
                        case 251:
                            $user = User::find($input['user_id']);
                            $user->sospecha=1;
                            $user->save();
                            Log::info('Usuario a sospecha: '. $user->user_id );
                            throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro mÃ©todo de pago.");
                            break;
                        case 252:
                            $user = User::find($input['user_id']);
                            $user->sospecha=1;
                            $user->save();
                            Log::info('Usuario a sospecha: '. $user->user_id );
                            throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro mÃ©todo de pago.");
                            break;
                        case 253:
                            $user = User::find($input['user_id']);
                            $user->sospecha=1;
                            $user->save();
                            Log::info('Usuario a sospecha: '. $user->user_id );
                            throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro mÃ©todo de pago.");
                            break;
                        case 260:
                            throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro mÃ©todo de pago.");
                            break;
                        case 261:
                            throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro mÃ©todo de pago.");
                            break;
                        case 262:
                            throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro mÃ©todo de pago.");
                            break;
                        case 263:
                            throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro mÃ©todo de pago.");
                            break;
                        case 264:
                            throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro mÃ©todo de pago.");
                            break;
                        case 300:
                            throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro mÃ©todo de pago.");
                            break;
                        case 400:
                            throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro mÃ©todo de pago.");
                            break;
                        case 410:
                            throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro mÃ©todo de pago.");
                            break;
                        case 411:
                            throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro mÃ©todo de pago.");
                            break;
                        case 420:
                            throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro mÃ©todo de pago.");
                            break;
                        case 421:
                            throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro mÃ©todo de pago.");
                            break;
                        case 430:
                            throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro mÃ©todo de pago.");
                            break;
                        case 440:
                            throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro mÃ©todo de pago.");
                            break;
                        case 441:
                            throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro mÃ©todo de pago.");
                            break;
                        case 460:
                            throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro mÃ©todo de pago.");
                            break;
                        case 461:
                            throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro mÃ©todo de pago.");
                            break;
                        default:
                            # sino es ninguno de los parametros actuales de BAC
                            throw new Exception("Error en la TransacciÃ³n bancaria. Intente con otro mÃ©todo de pago.");
                            break;
                    }
                }elseif ($datosUsuario[0]['sospecha']==1){
                    throw new Exception("Orden incobrable. Intente con otro mÃ©todo de pago.");
                }elseif ($payment_method->payment_method_id == 1) {
                    ///////////////Efectivo
                    $order->pay_bill = $input['pay_bill'];
                    $pago = round(($order->pay_bill),2);
                    $totalOrden =round((substr($order->order_total, 1)),2);
                    $costoEnvio = round($order->shipping_charge,2);
                    $totalCliente = ($totalOrden + $costoEnvio);
                    $cambioCliente = $pago - $totalCliente;
                    if (round($totalCliente,2) == round($pago,2) ) {
                        $order->pay_change = 0;
                    } else {
                        $order->pay_change = $pago - $totalCliente;
                    }
                if($order->pay_change < 0)
                    throw new Exception("Debe ingresar una cantidad igual o mayor a la del precio total");
                }elseif ($payment_method->payment_method_id == 3) {
                    ///////////////Tigo Money
                    $subtotal = substr($order->order_total,1);
                    $order->billetera_user = $input['billetera_user'];
                    $order->num_tigo_money = $input['num_tigo_money'];
                    $tigo = DB::table('res_payment_methods')->where('payment_method_id', 3)->first();
                    $tigo_money_charge=round((($subtotal + $order->shipping_charge) * $tigo->payment_method_charge),6);
                    //aproximando  0.17575 a 0.18
                    $tigo_money_charge= $tigo_money_charge+0;
                    if($tigo_money_charge != 0){
                        $cad = explode(".", $tigo_money_charge);
                        $rest= substr($cad[1], 2, 1);
                        if ($rest<5 && $rest!=0 && $rest!=null) {
                           $tigo_money_charge=round(($tigo_money_charge+0.01),2);
                        } else {
                            $tigo_money_charge=round(($tigo_money_charge),2);
                        }
                    }else{
                        $tigo_money_charge = 0;
                    }
                    $order->tigo_money_charge = $tigo_money_charge;
                }
                ///////////////banca fin

                //Primer estado si es efectivo
                if ($payment_method->payment_method_id == 1){
                    $order->save();
                    // Asignar el primer estado a la orden
                    $order_stat = new OrderStatusLog;
                    $order_stat->order_id = $order->order_id;
                    $order_stat->user_id = $user_id;
                    $order_stat->order_status_id = 1;
                    $order_stat->save();
                }
                //Estado 13 si es Tigo Money
                if ($payment_method->payment_method_id == 3){
                    $order->save();
                    // Asignar el estado 13 a la orden
                    $order_stat = new OrderStatusLog;
                    $order_stat->order_id = $order->order_id;
                    $order_stat->user_id = $user_id;
                    $order_stat->order_status_id = 13;
                    $order_stat->save();
                }

                // Asignar la orden al usuario
                $order_usr = new OrderRating;
                $order_usr->order_id = $order->order_id;
                $order_usr->user_id = $user_id;
                $order_usr->quality_rating = 0;
                $order_usr->speed_rating = 0;
                $order_usr->comment = '';
                $order_usr->rating_date = "0000-00-00 00:00:00";
                $order_usr->save();

                //Total de la orden por defecto
                $order_total = 0;
                foreach ($input['products'] as $product) {
                    $prd = Product::findOrFail($product['product_id']);
                    $order_det = new OrderDetail;
                    $order_det->order_id = $order->order_id;
                    $order_det->product_id = $prd->product_id;
                    $order_det->quantity = $product['quantity'];
                    $order_det->product = $prd->product;
                    $order_det->unit_price = $prd->value;
                    $order_det->total_price = ($product['quantity'] * $prd->value);
                    $order_det->comment = (isset($product['comment']) ? $product['comment'] : '');
                    $order_det->save();

                    if (isset($product['ingredients']) && !empty($product['ingredients'])) {
                        foreach ($product['ingredients'] as $ingredient) {
                            //Verificando los ingredientes de la orden
                            $ingr = Ingredient::findOrFail($ingredient['ingredient_id']);

                            $detalle_ingrediente = new OrderDetailProductIngredient;
                            $detalle_ingrediente->order_det_id = $order_det->order_det_id;
                            $detalle_ingrediente->ingredient_id = $ingr->ingredient_id;
                            $detalle_ingrediente->ingredient = $ingr->ingredient;
                            if ($ingredient['selected'] == 1)
                                $detalle_ingrediente->remove = 0;
                            else
                                $detalle_ingrediente->remove = 1;

                            $detalle_ingrediente->save();
                        }
                    }

                    if (isset($product['options']) && !empty($product['options'])) {
                        //verificando las condiciones que hay en el producto
                        foreach ($product['options'] as $condition) {
                            $detalle_opcion = new OrderDetailProductCondition;
                            $detalle_opcion->order_det_id = $order_det->order_det_id;
                            $detalle_opcion->condition_id = $condition['condition_id'];
                            $cond = Condition::findOrFail($condition['condition_id']);
                            $detalle_opcion->condition = $cond->condition;
                            $detalle_opcion->condition_option_id = $condition['condition_option_id'];
                            $opti = ConditionOption::findOrFail($condition['condition_option_id']);
                            $detalle_opcion->condition_option = $opti->condition_option;
                            $detalle_opcion->save();
                        }
                    }
                    $order_total = ($order_total + $order_det->total_price);
                }/////End foreach products
                $response = array(
                    "status" => true,
                    "data" => array("order_id" => $order->order_id)
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

    //Nueva función que alamacena la orden
    public function makeStore() {
        //variables globales para la función
        $input = Input::all();
        $activate=0; //Representa la aplicación de descuento por medio de bins
        $total = 0; //total por producto
        Log::info('Antes');
        try {
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
        
        }catch(Exception $e){
            Log::info('error bin manejado');
        }
        Log::info('Despues');
        

          try {
            $statusCode = 200;
            $user_id = $input['user_id'];
            $order = new Order;
            $order->customer = (isset($input['customer']) ? $input['customer'] : '');
            $order->customer_phone = (isset($input['customer_phone']) ? $input['customer_phone'] : '');
            $order->restaurant_id = $input['restaurant_id'];

            //Seter de los primeros productos
            foreach ($input['products'] as $product) {
                $prd = Product::findOrFail($product['product_id']);
                $total = $total + ($prd->value*$product['quantity']);
            }
            //se aplica el descuento al producto si el bin es activo y si el método de pago es por tarjeta
            if ($activate == 1  && $input['payment_method_id'] == 2) {
                $descuentoMonto = round(($total * $porcentaje/100),2);
                $total = round(($total - $descuentoMonto),2);
            }
            //Se asigna el primer total
            $order->order_total = $total;
            $order->service_type_id = $input['service_type_id'];
            //Si es cualquiera de los dos servicios a domicilio
            if ($order->service_type_id == 1 || $order->service_type_id == 3) {
                if(!Restaurant::isOpen($order->restaurant_id, $order->service_type_id))
                    throw new Exception("Lo sentimos, en este momento el restaurante se encuentra fuera de nuestro horario de servicio. Le solicitamos verificar los horarios en la sección Información del Restaurante.");

                //Seteando Direccióm
                $direccion = Address::where('address_id', $input['address_id'])->first();
                $addr = $direccion->address_name . ' - ' . $direccion->address_1 .
                            (!empty($direccion->address_2) ? ', ' . $direccion->address_2 : '');
                $order->address = $addr;
                $order->address_id=$direccion->address_id;

                //Definiendo costo de envío
                if ($order->service_type_id == 1) {
                    $restaurant = Restaurant::findOrFail($order->restaurant_id);
                    $order->shipping_charge = $restaurant->shipping_cost;

                    if ($activate == 1  && $input['payment_method_id'] == 2) {
                        $descuentoMonto = round(($restaurant->shipping_cost * $porcentaje/100),2);
                        $order->shipping_charge = round(($restaurant->shipping_cost - $descuentoMonto),2);
                    }

                } else {
                    $zone = DB::table('restaurants_zones')->where('zone_id', $direccion->zone_id)->where('restaurant_id', $order->restaurant_id)->first();

                /***************CALCULO EL FREE SHIPPING SI APLICA****************/
                    $free =  FreeShippingRestaurants::where('restaurant_id','=',$input['restaurant_id'])->get();
                    $contArr = count($free);
                    $total = $order->order_total;
                    $totalF = str_replace("$","",$total);

                    Log::info('Total controller2: '.$totalF);

                    if($contArr > 0) {
                        foreach ($free as $value) {
                            $arreglo = $value;
                        }
                        Log::info("Monto minimo2: ".$arreglo->monto_minimo);
                        $conteo = count($arreglo);
                        if($conteo > 0 && $totalF >= $arreglo->monto_minimo){
                            Log::info("entra al if conteo");
                            $order->shipping_charge = 0.0;
                        }else{
                            Log::info("NO entra al if conteo");

                            if($zone==NULL){
                                $order->shipping_charge = 0;
                            }
                            else if ($activate == 1 && $input['payment_method_id'] == 2) {
                                $descuentoMonto = round(($zone->shipping_charge * $porcentaje/100),2);
                                $order->shipping_charge = round(($zone->shipping_charge - $descuentoMonto),2);
                            }else {
                                $order->shipping_charge = $zone->shipping_charge;;
                            }
                        }
                    }else{
                        Log::info("NO entra al if conteo");
                        if($zone==NULL){
                            $order->shipping_charge = 0;
                        }
                        else if ($activate == 1 && $input['payment_method_id'] == 2) {

                            $descuentoMonto = round(($zone->shipping_charge * $porcentaje/100),2);
                            $order->shipping_charge = round(($zone->shipping_charge - $descuentoMonto),2);
                        }else {
                            $order->shipping_charge = $zone->shipping_charge;;
                        }
                    }
                    /*************FIN FREE DELIVERY*************/
                }

            //Si es para llevar
            } else {
                $order->restaurant_id = $input['res_address_id'];
                $order->pickup_hour = $input['pickup_hour'];
                $order->pickup_min = $input['pickup_min'];
                $order->shipping_charge = 0;
                if(!Restaurant::isOpen($order->restaurant_id,$order->service_type_id,$order->pickup_hour.':'.$order->pickup_min.':00')){
                    throw new Exception("Lo sentimos, en este momento el restaurante se encuentra fuera de nuestro horario de servicio. Le solicitamos verificar los horarios en la sección Información del Restaurante.");
                }
                $direccion = Restaurant::where('restaurant_id', $order->restaurant_id)->first();
                $addr = $direccion->name . ' - ' . $direccion->address;
                $order->address = $addr;
            }

            ///Guardando Origen del Dispositivo
            if (isset($input['source_device'])) {
                $order->source_device=$input['source_device'];
            }

            $usuario = User::where('user_id', $user_id)->get();
            $datosUsuario = json_decode($usuario, true);
            //SI el usuario tiene sospecha se entiende que no puede procesar pagos
            if($datosUsuario[0]['sospecha']==1){
                throw new Exception("Orden incobrable. Intente con otro método de pago.");
            }
            //Manejo de Ordenes iOS y androids olds
            $amex= substr($input['credit_card'], 0, 2);
            log::info('amex: '.$amex);
            $payment_method = PaymentMethod::findOrFail($input['payment_method_id']);
            $order->payment_method_id = $payment_method->payment_method_id;

            //si el pago es por tarjeta y es AMERICAN EXPRESS
            if ($payment_method->payment_method_id == 2 && $datosUsuario[0]['sospecha']!=1 && ($amex =='37'||$amex=='34') && ($input['source_device']=='Android' || $input['source_device']=='iOS')) {
                $order->credit_name = $input['credit_name'];
                $order->credit_card = $input['credit_card'];
                $order->credit_expmonth = $input['credit_expmonth'];
                $order->credit_expyear = $input['credit_expyear'];
                $order->secure_code = $input['secure_code'];
                $card = DB::table('res_payment_methods')->where('payment_method_id', 2)->first();
                $subtotal = substr($order->order_total,1);
                $credit_charge = round((($subtotal + $order->shipping_charge) * $card->payment_method_charge),6);
                $tarjetaNoEncript=1;
                $credit_charge= $credit_charge+0;
                $cad = explode(".", $credit_charge);
                $rest= substr($cad[1], 2, 1);
                $credit_charge=round(($credit_charge),2);
                $order->order_cod = $this->numeroOrden();
                $order->credit_charge = $credit_charge;
                $order->save();

                if ($activate == 1) {
                    $descuentoMonto = round(($credit_charge * $porcentaje/100),2);
                    $credit_charge = round(($credit_charge - $descuentoMonto),2) +0.02;
                    $order->credit_charge =$credit_charge;
                    $order->save();

                }

                $order_stat = new OrderStatusLog;
                $order_stat->order_id = $order->order_id;
                $order_stat->user_id = $user_id;
                $order_stat->order_status_id = 2;
                $order_stat->save();
            }
            //si el pago es por efectivo
          if ($payment_method->payment_method_id == 1) {
                ///////////////Efectivo
                $order->pay_bill = $input['pay_bill'];
                $pago = round(($order->pay_bill),2);
                $totalOrden =round((substr($order->order_total, 1)),2);
                $costoEnvio = round($order->shipping_charge,2);
                $totalCliente = ($totalOrden + $costoEnvio);
                $cambioCliente = $pago - $totalCliente;
                if (round($totalCliente,2) == round($pago,2) ) {
                    $order->pay_change = 0;
                } else {
                    $order->pay_change = $pago - $totalCliente;
                }
                if($order->pay_change < 0)
                    throw new Exception("Debe ingresar una cantidad igual o mayor a la del precio total");
            }
            //si el pago es por tigo money
            if ($payment_method->payment_method_id == 3){
                $subtotal = substr($order->order_total,1);
                $order->billetera_user = $input['billetera_user'];
                $order->num_tigo_money = $input['num_tigo_money'];
                $tigo = DB::table('res_payment_methods')->where('payment_method_id', 3)->first();
                $tigo_money_charge=round((($subtotal + $order->shipping_charge) * $tigo->payment_method_charge),6);
                //aproximando  0.17575 a 0.18
                $tigo_money_charge= $tigo_money_charge+0;
                if($tigo_money_charge != 0){
                    $cad = explode(".", $tigo_money_charge);
                    $rest= substr($cad[1], 2, 1);
                    if ($rest<5 && $rest!=0 && $rest!=null) {
                       $tigo_money_charge=round(($tigo_money_charge+0.01),2);
                    } else {
                        $tigo_money_charge=round(($tigo_money_charge),2);
                    }
                }else{
                    $tigo_money_charge = 0;
                }
                $order->tigo_money_charge = $tigo_money_charge;
            }

            $order->order_cod = $this->numeroOrden();
            $usuario = User::where('user_id', $user_id)->get();
            $datosUsuario = json_decode($usuario, true);

            ///Manejo Ordenes Android
            if (isset($tarjetaNoEncript)) {
                $tarjetaNoEncript=1;
            } else {
                $tarjetaNoEncript=0;
            }

            //Si es por tarjeta pero NO ES AMERICAN EXPRESS
            if ($payment_method->payment_method_id == 2 && $datosUsuario[0]['sospecha']!=1 && ($input['source_device']=='Android' || $input['source_device']=='iOS') && $tarjetaNoEncript!=1) {

                $order->credit_name = $input['credit_name'];
                $order->credit_card = $input['credit_card'];
                $order->credit_expmonth = $input['credit_expmonth'];
                $order->credit_expyear = $input['credit_expyear'];
                $order->secure_code = $input['secure_code'];
                $card = DB::table('res_payment_methods')->where('payment_method_id', 2)->first();
                $subtotal = substr($order->order_total,1);
                $credit_charge = round((($subtotal + $order->shipping_charge) * $card->payment_method_charge),6);
                $credit_charge= $credit_charge+0;
                $cad = explode(".", $credit_charge);
                $rest= substr($cad[1], 2, 1);
                $credit_charge=round(($credit_charge),2);
                $order->credit_charge = $credit_charge;
                //Obteniendo los parametros del banco desde la tabla pf.bank_option
                $bank_option = BankOption::findOrFail(1);
                $password = $bank_option->username_password;
                $username = $bank_option->username_bank;
                $type = $bank_option->type_bank;
                $key_BAC = $bank_option->key_bank;//Key privado de la cuenta bancaria,
                $key_ID_BAC = $bank_option->key_id_bank;// Key publica
                $UnixTime = time();
                $amount = round((floatval(substr($order->order_total, 1))),2) + round(($order->shipping_charge),2)+$order->credit_charge +0.01;
                $ccnumber = $order->credit_card = $input['credit_card']; //dummy visa
                $ccexp = $order->credit_expmonth.substr($order->credit_expyear,-2);// contruyendo mes/año con 2 digitos
                $cvv = $order->secure_code = $input['secure_code'];
                $bank_url = $bank_option->url_post_bank;
                $amount =number_format((float)$amount, 2, '.', '');

                Log::info('Tarjeta encriptada: '.$order->credit_card);
                Log::info('Total de la compra: '.$amount);
                Log::info('Tiempo Unix '.$UnixTime);
                $today = date("Y-m-d\ H:i:s",$UnixTime);
                log::info('Tiempo de envío: '. $today);
                Log::info('Usuario de la BD: '.$username);
                Log::info('password BD: '.$password);
                Log::info('Número de Orden: '.$order->order_cod);
                Log::info('llave del banco: '.$bank_option->key_bank);
                // Send an asynchronous request.
                $client = new GuzzleHttp\Client();
                $req = $client->createRequest('POST', $bank_url,[
                  'body'=>[
                          'username' => $username,
                          'password'=>$password,
                          'type'=>$type,
                          'amount'=>$amount,
                          'encrypted_payment'=>$order->credit_card
                        ]
                ]);
                $responseG=$client->send($req);
                $response = explode('&',$responseG);
                $respuestaBAC = array();
                foreach($response AS $key => $value){
                    $newValues = explode('=',$value);
                    $respuestaBAC[$newValues[0]] = $newValues[1];
                }

                Log::info('respuestaBAC: '.$responseG);
                Log::info('transactionid: '.$respuestaBAC['transactionid']);
                Log::info('response_code: '.$respuestaBAC['response_code']);
                Log::info('responsetext: '.$respuestaBAC['responsetext']);

                switch ($respuestaBAC['response_code']) {
                    case 100:
                    # Transaccion acceptada...
                        $order->credit_card='Encriptada';
                        $order->transaction_id = $respuestaBAC['transactionid'];
                        $order->save();
                        $order_stat = new OrderStatusLog;
                        $order_stat->order_id = $order->order_id;
                        $order_stat->user_id = $user_id;
                        $order_stat->order_status_id = 2;
                        $order_stat->save();
                        break;
                    case 200:
                    case 201:
                    case 203:
                    case 204:
                    case 220:
                    case 221:
                    case 222:
                    case 240:
                    case 250:
                    case 260:
                    case 261:
                    case 262:
                    case 263:
                    case 264:
                    case 300:
                    case 400:
                    case 410:
                    case 411:
                    case 420:
                    case 421:
                    case 430:
                    case 440:
                    case 441:
                    case 460:
                    case 461:
                        throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
                        break;
                    case 202:
                        throw new Exception("Lo sentimos, Fondos insuficientes. Intente con otro método de pago.");
                        break;
                    case 223:
                        throw new Exception("Lo sentimos, Su tarjeta esta expirada. Intente con otro método de pago.");
                        break;
                    case 224:
                        throw new Exception("Fecha de vencimiento inválida, verifique sus datos");
                        break;
                    case 225:
                        throw new Exception("Código de serguridad inválido, verifique sus datos");
                        break;
                    case 251:
                    case 252:
                    case 253:
                        $user = User::find($input['user_id']);
                        $user->sospecha=1;
                        $user->save();
                        Log::info('Usuario a sospecha: '. $user->user_id );
                        throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
                        break;
                    default:
                        # sino es ninguno de los parametros actuales de BAC
                        throw new Exception("Error en la Transacción bancaria. Intente con otro método de pago.");
                        break;
                }
            }

            //Primer estado si es efectivo
            if ($payment_method->payment_method_id == 1){
                $order->save();
                // Asignar el primer estado a la orden
                $order_stat = new OrderStatusLog;
                $order_stat->order_id = $order->order_id;
                $order_stat->user_id = $user_id;
                $order_stat->order_status_id = 1;
                $order_stat->save();
            }
            //Estado 13 si es Tigo Money
            if ($payment_method->payment_method_id == 3){
                $order->save();
                // Asignar el estado 13 a la orden
                $order_stat = new OrderStatusLog;
                $order_stat->order_id = $order->order_id;
                $order_stat->user_id = $user_id;
                $order_stat->order_status_id = 13;
                $order_stat->save();
            }

            // Asignar la orden al usuario
            $order_usr = new OrderRating;
            $order_usr->order_id = $order->order_id;
            $order_usr->user_id = $user_id;
            $order_usr->quality_rating = 0;
            $order_usr->speed_rating = 0;
            $order_usr->comment = '';
            $order_usr->rating_date = "0000-00-00 00:00:00";
            $order_usr->save();

            //Total de la orden por defecto
            $order_total = 0;
            foreach ($input['products'] as $product) {
                $prd = Product::findOrFail($product['product_id']);
                $order_det = new OrderDetail;
                $order_det->order_id = $order->order_id;
                $order_det->product_id = $prd->product_id;
                $order_det->quantity = $product['quantity'];
                $order_det->product = $prd->product;
                $order_det->unit_price = $prd->value;
                $order_det->total_price = ($product['quantity'] * $prd->value);
                $order_det->comment = (isset($product['comment']) ? $product['comment'] : '');
                $order_det->save();

                if (isset($product['ingredients']) && !empty($product['ingredients'])) {
                    foreach ($product['ingredients'] as $ingredient) {
                        //Verificando los ingredientes de la orden
                        $ingr = Ingredient::findOrFail($ingredient['ingredient_id']);

                        $detalle_ingrediente = new OrderDetailProductIngredient;
                        $detalle_ingrediente->order_det_id = $order_det->order_det_id;
                        $detalle_ingrediente->ingredient_id = $ingr->ingredient_id;
                        $detalle_ingrediente->ingredient = $ingr->ingredient;
                        if ($ingredient['selected'] == 1)
                            $detalle_ingrediente->remove = 0;
                        else
                            $detalle_ingrediente->remove = 1;

                        $detalle_ingrediente->save();
                    }
                }

                if (isset($product['options']) && !empty($product['options'])) {
                    //verificando las condiciones que hay en el producto
                    foreach ($product['options'] as $condition) {
                        $detalle_opcion = new OrderDetailProductCondition;
                        $detalle_opcion->order_det_id = $order_det->order_det_id;
                        $detalle_opcion->condition_id = $condition['condition_id'];
                        $cond = Condition::findOrFail($condition['condition_id']);
                        $detalle_opcion->condition = $cond->condition;
                        $detalle_opcion->condition_option_id = $condition['condition_option_id'];
                        $opti = ConditionOption::findOrFail($condition['condition_option_id']);
                        $detalle_opcion->condition_option = $opti->condition_option;
                        $detalle_opcion->save();
                    }
                }
                $order_total = ($order_total + $order_det->total_price);
            }/////End foreach products
            $response = array(
                "status" => true,
                "data" => array("order_id" => $order->order_id)
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
     * Display the specified resource.
     * GET /order/{id}
     *
     * @param  int  $id
     * @return Response
     */
    public function show() {
        $input = Input::all();
        try {
            $statusCode = 200;
            $order = Order::findOrFail($input['order_id']);
            $order->products = OrderDetail::where('order_id', $order->order_id)
                            ->with('conditions')->with('ingredients')->get();
            $status_log = OrderStatusLog::where('order_id', $order->order_id)
                            ->with('orderStatus')->orderBy('order_status_id', 'desc')->first();
            $order->status_logs = array(
                'order_status' => $status_log->order_status->order_status,
                'created_at' => date('d/m/Y h:ia', strtotime($status_log->created_at))
            );
            $total = (substr($order->order_total, 1) + $order->shipping_charge + $order->credit_charge);
            $order->order_total = number_format($total,2,",",".");
            $restaurant = Restaurant::find($order->restaurant_id);
            $order->restaurant_name = $restaurant->name;
            $response = array(
                "status" => true,
                "data"  => $order
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
     * Show the form for editing the specified resource.
     * GET /order/{id}/edit
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     * PUT /order/{id}
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     * DELETE /order/{id}
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id) {
        //
    }

    public function shipping_charge() {
        $input = Input::all();
        try {
            $statusCode = 200;

            $zone = DB::table('restaurants_zones')->where('zone_id', $input['zone_id'])->where('restaurant_id', $input['restaurant_id'])->first();

            $response = array(
                "status" => true,
                "data" => $zone
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

    public function change_status() {
        $input = Input::all();
        try {
            $statusCode = 200;
            $order_status_log = new OrderStatusLog();
            $order_status_log->order_id=$input['order_id'];
            $order_status_log->order_status_id=$input['order_status_id'];
            $order_status_log->user_id=$input['user_id'];
            $order_status_log->comment = $input['comment'];

            $order_status_log->save();

            // Actualizar el status de la orden si ha sido completada o rechazada
            if ($input['order_status_id'] == 5 || $input['order_status_id'] == 7) {

                DB::table('req_order_motorista')
                    ->where('order_id', $input['order_id'])
                    ->where('motorista_id', $input['motorista_id'])
                    ->delete();

                DB::table('motoristas')
                    ->where('motorista_id', $input['motorista_id'])
                    ->update(['estado' => 0]);
            }

            $response = array(
                "status" => true,
                "data" => $order_status_log
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
