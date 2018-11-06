<?php

class OrderController extends \BaseController {

    /**
     * Display a listing of the resource.
     * GET /order
     *
     * @return Response
     */
    public function index($datos) {
        $dato = explode(",", $datos);        
        //$input[0]= user_id
        //$input[1]= restaurant_id
        $user_id = $dato[0];
        $restaurant_id = $dato[1];
        Log::info($dato);
        Log::info($user_id);
        Log::info($restaurant_id);
        try{
            $statusCode = 200;

            if(isset($input['page_size']) && $input['page_size'] > 0){
                $pagesze = $input['page_size'];
                $pagepos = $input['page_post'];
                $orders = Order::join('req_order_ratings', function($join) use($user_id) {
                    $join->on('req_orders.order_id', '=', 'req_order_ratings.order_id')
                    ->where('req_order_ratings.user_id', '=', $user_id)->where('req_orders.restaurant_id','=', 56);
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
            }else{
                $orders = Order::join('req_order_ratings', function($join) use($user_id) {
                    $join->on('req_orders.order_id', '=', 'req_order_ratings.order_id')
                    ->where('req_order_ratings.user_id', '=', $user_id)->where('req_orders.restaurant_id','=', 56);
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
                }else{
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
        if($uc == 1)
            $source .= 'abcdefghijklmnopqrstuvwxyz';
        if($n == 1)
            $source .= '23456789';
        if($sc == 1)
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

    public function store2(){
        //if(!empty($idusuario) && $req_restaurant != 'name' && is_numeric($req_restaurant)){
        //verificamos si corresponde al id de restaurante
        //realizamos conteo de direcciones
        $idusuario = Input::get('user_id');
        $nuevaorden = new Order; //creamos una nueva orden
        $service_type_id = Input::get('service_type_id');

        /*CODIGO PARA INSERTAR NOMBRE Y TELEFONO DEL USUARIO A LA ORDEN*/
        $nuevaorden->customer = Input::get('nombre_user');
        $nuevaorden->customer_phone = Input::get('telefono_user');

        $idRes = DB::Table('res_restaurants')
            ->select('parent_restaurant_id')
            ->where('restaurant_id', Input::get('restaurant_id'))
            ->get();

        $service_type = ServiceType::findOrFail($service_type_id);
        $nuevaorden->service_type_id = $service_type->service_type_id;

        if($service_type->service_type_id == 1 || $service_type->service_type_id == 3) {

            $nuevaorden->restaurant_id = $idRes[0]->parent_restaurant_id;
            $addresses = Address::where('user_id', Input::get('user_id'))->get();
            
            if(count($addresses) == 1){
                //si existe solo una dirección se trabajará con ella
                $direccion = $addresses[0];
            }else{
                // Si esxiste más de una dirección se espera parámetro para decidir con cual trabajará
                $direccion = (Input::has('address_id'))? Address::where('address_id', Input::get('address_id'))->first():$addresses[0];
            }

            $nuevaorden->address = $direccion->address_name . ' - ' . $direccion->address_1 .
                (!empty($direccion->address_2) ? ', ' . $direccion->address_2 : '');

            $nuevaorden->address_id=$direccion->address_id;

            //Definiendo costo de envío
            if($nuevaorden->service_type_id == 1){
                $restaurant = Restaurant::findOrFail($nuevaorden->restaurant_id);
                $nuevaorden->shipping_charge = $restaurant->shipping_cost;
            }else{
                $zone = DB::table('restaurants_zones')
                    ->where('zone_id', $direccion->zone_id)->where('restaurant_id', $nuevaorden->restaurant_id)->first();
                $nuevaorden->shipping_charge = ($zone==NULL)? 0: $zone->shipping_charge;
            }
        }elseif($service_type->service_type_id == 2){
            $restaurant_address = Input::get('restaurant_address');
            if(!empty($restaurant_address)) {
                $direccion = Restaurant::where('restaurant_id', $restaurant_address)->firstOrFail();
                $addr = $direccion->name . ' - ' . $direccion->address;
                $nuevaorden->restaurant_id = $restaurant_address;
                $nuevaorden->address = $addr;
                $nuevaorden->pickup_hour = Input::get('hour');
                $nuevaorden->pickup_min = Input::get('minutes');
            }else
                return 'Falta especificar la dirección'; //Falta mandar el parametro
        }

        /*Se recorren los productos para crear el order_total*/
        $totalF = 0;
        /*if(is_array($req_order)){
            $totalF = 0;
            foreach ($req_order as $key => $req_product) {
                $product = Product::findOrFail($req_product['product_id']);
                $nuevo_total = ($product->value * $req_product['quantity']);
                $totalF += $nuevo_total;
            }
            $nuevaorden->order_total = $totalF;
        }*/
        $nuevaorden->order_total = $totalF;
        $payment_method_id = Input::get('payment_method_id');
        if(!empty($payment_method_id)) {//verificando el campo payment_method
            $payment_method = PaymentMethod::findOrFail($payment_method_id);
            $nuevaorden->payment_method_id = $payment_method->payment_method_id;
            
            $datosUsuario = User::where('user_id', $idusuario)->get();

            if($payment_method->payment_method_id == 2 && $datosUsuario[0]['sospecha'] != 1){//Si es pago con tarjeta
                $nuevaorden->credit_name = Input::get('user_credit');
                $nuevaorden->credit_card = Input::get('credit_card');
                $nuevaorden->credit_expmonth = Input::get('month');
                $nuevaorden->credit_expyear = Input::get('year');
                $nuevaorden->secure_code = Input::get('secure_code');

                $card = DB::table('res_payment_methods')->where('payment_method_id', 2)->first();
                $credit_charge = round((($totalF + $nuevaorden->shipping_charge) * $card->payment_method_charge),2);
                /*cargo de tarjeta*/
                $credit_charge= $credit_charge+0;
                $cad = explode(".", $credit_charge);

                log::info('cad: '.$cad[1]);
                $rest= substr($cad[1], 2, 1); //abcd
                
                log::info('rest TC: '.$rest);

                if($rest != null && $rest < 5) {
                    $credit_charge=round(($credit_charge + 0.01),2);
                    log::info('SI:credit_charge: '.$credit_charge);
                }else{
                    $credit_charge=round(($credit_charge + 0.01),2);
                    log::info('NO:credit_charge: '.$credit_charge);
                }

                $nuevaorden->credit_charge = $credit_charge;
                log::info('Sin aprox. cargo credit_charge: '.round((($totalF + $nuevaorden->shipping_charge) * $card->payment_method_charge),6));
                log::info('cargo credit_charge: '.$nuevaorden->credit_charge);

                /*Fin cargo tarjeta*/
                #$nuevaorden->credit_charge = $credit_charge;
                $transaccion = Input::get('transaction_id');
                //Se ingresa el id de la transacción
                if(isset($transaccion)){
                    $nuevaorden->transaction_id = Input::get('transaction_id');    
                }

                $nuevaorden->order_cod = $this->numeroOrden();
                $nuevaorden->source_device = 'app-web';
                $nuevaorden->save(); //Se hace update de precio final

                $order_stat = new OrderStatusLog;
                $order_stat->order_id = $nuevaorden->order_id;
                $order_stat->user_id = $idusuario;
                $order_stat->order_status_id = 2;
                $order_stat->save();

                // Asignar la orden al usuario
                $order_usr = new OrderRating;
                $order_usr->order_id = $nuevaorden->order_id;
                $order_usr->user_id = $idusuario;
                $order_usr->quality_rating = 0;
                $order_usr->speed_rating = 0;
                $order_usr->comment = '';
                $order_usr->rating_date = "0000-00-00 00:00:00";
                $order_usr->save();
                /*FIN CODIGO PAGO BAC*/
            }elseif($payment_method->payment_method_id == 2 && $datosUsuario[0]['sospecha']==1){
                return Redirect::to('cart/checkout')
                    ->withErrors('Orden incobrable. Intente con otro método de pago.');
            }elseif($payment_method->payment_method_id == 1){
                $nuevaorden->pay_bill = Input::get('cash');
                $nuevaorden->order_cod = $this->numeroOrden();
                $nuevaorden->source_device = 'Web';
                $nuevaorden->save(); // Se guarda la orden padre

                $order_stat = new OrderStatusLog;
                $order_stat->order_id = $nuevaorden->order_id;
                $order_stat->user_id = $idusuario;
                $order_stat->order_status_id = 1;
                $order_stat->save();

                // Asignar la orden al usuario
                $order_usr = new OrderRating;
                $order_usr->order_id = $nuevaorden->order_id;
                $order_usr->user_id = $idusuario;
                $order_usr->quality_rating = 0;
                $order_usr->speed_rating = 0;
                $order_usr->comment = '';
                $order_usr->rating_date = "0000-00-00 00:00:00";
                $order_usr->save();
            }elseif($payment_method->payment_method_id == 3){//COMIENZA CODIGO PARA TIGO MONEY
                $nuevaorden->order_cod = $this->numeroOrden();
                $nuevaorden->num_tigo_money = Input::get('num_debitar');
                $nuevaorden->billetera_user = Input::get('billetera');
                #$nuevaorden->tigo_money_charge = Input::get('cargo_uso_tigo');

                $tigo = DB::table('res_payment_methods')->where('payment_method_id', 3)->first();
                $tigo_charge = round((($totalF + $nuevaorden->shipping_charge) * $tigo->payment_method_charge),2);
                
                /*cargo de tigo*/
                $tigo_charge= $tigo_charge+0;
                $cad = explode(".", $tigo_charge);

                log::info('cad: '.$cad[1]);
                $rest= substr($cad[1], 2, 1); // abcd
                
                log::info('rest TM: '.$rest);

                if($rest != null && $rest < 5) {
                    $tigo_charge=round(($tigo_charge + 0.01),2);
                    log::info('SI:tigo_charge: '.$tigo_charge);
                }else{
                    $tigo_charge=round(($tigo_charge+0.01),2);
                    log::info('NO:tigo_charge: '.$tigo_charge);
                }

                $nuevaorden->tigo_money_charge = $tigo_charge;
                log::info('Sin aprox. cargo tigo_charge: '.round((($totalF + $nuevaorden->shipping_charge) * $tigo->payment_method_charge),6));
                log::info('cargo tigo_charge: '.$nuevaorden->tigo_money_charge);

                /*FIN TIGO*/
                $nuevaorden->source_device = 'app-web';
                $nuevaorden->save(); // Se guarda la orden padre

                $order_stat = new OrderStatusLog;
                $order_stat->order_id = $nuevaorden->order_id;
                $order_stat->user_id = $idusuario;
                $order_stat->order_status_id = 13;
                $order_stat->save();

                // Asignar la orden al usuario
                $order_usr = new OrderRating;
                $order_usr->order_id = $nuevaorden->order_id;
                $order_usr->user_id = $idusuario;
                $order_usr->quality_rating = 0;
                $order_usr->speed_rating = 0;
                $order_usr->comment = '';
                $order_usr->rating_date = "0000-00-00 00:00:00";
                $order_usr->save();
            }else
                return 'No existe tipo de pago';
        }else
            return 'No existe tipo de pago';
        #}
    }

    /**
     * Store a newly created resource in storage.
     * POST /order
     *
     * @return Response
     */
    public function store() {
        $datos = $_POST['info'];
        Log::info($datos['datosCompra']);
        
        try {
            $statusCode = 200;
            $user_id = $datos['datosCompra']['user_id'];
            $order = new Order;
            $order->customer = (isset($datos['datosCompra']['customer']) ? $datos['datosCompra']['customer'] : '');
            $order->customer_phone = (isset($datos['datosCompra']['customer_phone']) ? $datos['datosCompra']['customer_phone'] : '');
            $order->restaurant_id = $datos['datosCompra']['restaurant_id'];
    
            //Seter de los primeros productos
            $total = $datos['datosCompra']['pay_bill'];
            
            //esto lo tengo comentado
            foreach($datos['products'] as $product){
                $prd = Product::findOrFail($product['product_id']);
                $total = $total + ($prd->value*$product['quantity']);
            }

            $order->order_total = $datos['datosCompra']['subtotal'];
            $order->service_type_id = $datos['datosCompra']['service_type_id'];
            
            //Si es cualquiera de los dos servicios a domicilio
            if ($order->service_type_id == 1 || $order->service_type_id == 3) {

                if(!Restaurant::isOpen($order->restaurant_id, $order->service_type_id))
                    throw new Exception("Lo sentimos, en este momento el restaurante se encuentra fuera de nuestro horario de servicio. Le solicitamos verificar los horarios en la sección Información del Restaurante.");

                $addresses = Address::where('user_id', $user_id)->get();
                if (count($addresses) == 1) {
                    //si existe solo una dirección se trabajará con ella
                    $direccion = $addresses[0];
                } else {
                    // Si esxiste más de una dirección se espera parámetro para decidir con cual trabajará
                    $direccion = Address::where('address_id', $datos['datosCompra']['address_id'])->first();
                }
                $addr = $direccion->address_name . ' - ' . $direccion->address_1 .
                    (!empty($direccion->address_2) ? ', ' . $direccion->address_2 : '');

                $order->address = $addr;
                $order->address_id=$direccion->address_id;

                //Definiendo costo de envío
                if ($order->service_type_id == 1) {
                    $restaurant = Restaurant::findOrFail($order->restaurant_id);
                    $order->shipping_charge = $restaurant->shipping_cost;
                } else {
                    $zone = DB::table('restaurants_zones')->where('zone_id', $direccion->zone_id)->where('restaurant_id', $order->restaurant_id)->first();
                    $order->shipping_charge = ($zone==NULL)? 0: $zone->shipping_charge;
                }

                //Si es para llevar
            }else{
                $order->restaurant_id = $datos['datosCompra']['res_address_id'];
                $order->pickup_hour = $datos['datosCompra']['pickup_hour'];
                $order->pickup_min = $datos['datosCompra']['pickup_min'];

                $order->shipping_charge = 0;

                if(!Restaurant::isOpen(
                        $order->restaurant_id,
                        $order->service_type_id,
                        $order->pickup_hour.':'.$order->pickup_min.':00'
                    )
                ){
                    throw new Exception("Lo sentimos, en este momento el restaurante se encuentra fuera de nuestro horario de servicio. Le solicitamos verificar los horarios en la sección Información del Restaurante.");
                }

                $direccion = Restaurant::where('restaurant_id', $order->restaurant_id)->first();
                $addr = $direccion->name . ' - ' . $direccion->address;
                $order->address = $addr;
            }

            ///Guardando Origen del Dispositivo
            if (isset($input['source_device'])) {
               $order->source_device=$input['source_device'];
            }else{
              $order->source_device='ionic';
              $datos['datosCompra']['source_device']='ionic';
            }
            
            $usuario = User::where('user_id', $user_id)->get(); 
            $datosUsuario = json_decode($usuario, true);

            //Manejo de Ordenes iOS y androids olds
            $amex= substr($datos['datosCompra']['credit_card'], 0, 2);
            //log::info('amex: '.$amex);
            $payment_method = PaymentMethod::findOrFail($datos['datosCompra']['payment_method_id']);
            $order->payment_method_id = $payment_method->payment_method_id;
            if ($payment_method->payment_method_id == 2 && $datosUsuario[0]['sospecha']!=1 && ($amex =='37'||$amex=='34') && $input['source_device']=='Android') {
                $order->credit_name = $datos['datosCompra']['credit_name'];
                $order->credit_card = $datos['datosCompra']['credit_card'];
                $order->credit_expmonth = $datos['datosCompra']['credit_expmonth'];
                $order->credit_expyear = $datos['datosCompra']['credit_expyear'];
                $order->secure_code = $datos['datosCompra']['secure_code'];
                $card = DB::table('res_payment_methods')->where('payment_method_id', 2)->first();
                $subtotal = substr($order->order_total,1);
                $credit_charge = round((($subtotal + $order->shipping_charge) * $card->payment_method_charge),6);
                $tarjetaNoEncript=1;
                $credit_charge= $credit_charge+0;
                $cad = explode(".", $credit_charge);
              
                // log::info('cad: '.$cad[1]);
                $rest= substr($cad[1], 2, 1);  // abcd
                if ($rest!=null && $rest<5 && $rest!=0) {
                   $credit_charge=round(($credit_charge+0.01),2);
                   // log::info('SI:credit_charge: '.$credit_charge);
                } else {
                    $credit_charge=round(($credit_charge),2);
                    //log::info('NO:credit_charge: '.$credit_charge);
                }
                
                // log::info('rest: '.$rest);
                $order->order_cod = $this->numeroOrden();
                $order->credit_charge = $credit_charge;
                //log::info('Sin aprox. cargo credit_charge: '.round((($subtotal + $order->shipping_charge) * $card->payment_method_charge),6));
                //log::info('cargo credit_charge: '.$order->credit_charge);
                //$order->credit_card='Encriptada';
                $order->save();
                $order_stat = new OrderStatusLog;
                $order_stat->order_id = $order->order_id;
                $order_stat->user_id = $user_id;
                $order_stat->order_status_id = 2;
                $order_stat->save();
            } 
            if($payment_method->payment_method_id == 2 && $datosUsuario[0]['sospecha']!=1 && $datos['datosCompra']['source_device']!='Android') {
                $order->credit_name = $datos['datosCompra']['credit_name'];
                $order->credit_card = $datos['datosCompra']['credit_card'];
                $order->credit_expmonth = $datos['datosCompra']['credit_expmonth'];
                $order->credit_expyear = $datos['datosCompra']['credit_expyear'];
                $order->secure_code = $datos['datosCompra']['secure_code'];
                $card = DB::table('res_payment_methods')->where('payment_method_id', 2)->first();
                $subtotal = substr($order->order_total,1);
                $credit_charge = round((($subtotal + $order->shipping_charge) * $card->payment_method_charge),6);

                $credit_charge= $credit_charge+0;
                $cad = explode(".", $credit_charge);
              
                 //log::info('cad: '.$cad[1]);
                $rest= substr($cad[1], 2, 1);  // abcd
                if ($rest!=null && $rest<5 && $rest!=0) {
                   $credit_charge=round(($credit_charge+0.01),2);
                   // log::info('SI:credit_charge: '.$credit_charge);
                }else{
                    $credit_charge=round(($credit_charge),2);
                    //log::info('NO:credit_charge: '.$credit_charge);
                }
                
                // log::info('rest: '.$rest);
                $order->order_cod = $this->numeroOrden();
                $order->credit_charge = $credit_charge;
                //log::info('Sin aprox. cargo credit_charge: '.round((($subtotal + $order->shipping_charge) * $card->payment_method_charge),6));
                //log::info('cargo credit_charge: '.$order->credit_charge);
                //$order->credit_card='Encriptada';
                $order->save();
                $order_stat = new OrderStatusLog;
                $order_stat->order_id = $order->order_id;
                $order_stat->user_id = $user_id;
                $order_stat->order_status_id = 2;
                $order_stat->save();

            }elseif($payment_method->payment_method_id == 1){
                $order->pay_bill = $datos['datosCompra']['pay_bill'];
                // log::info('pay_bill: '.$order->pay_bill);
                //log::info('round(($order->pay_bill),2): '.round(($order->pay_bill),2));
                $pago = round(($order->pay_bill),2);
                $totalOrden =round((substr($order->order_total, 1)),2);
                $costoEnvio = round($order->shipping_charge,2);
                $totalCliente = ($totalOrden + $costoEnvio);
                $cambioCliente = $pago - $totalCliente;
                if(round($totalCliente,2) == round($pago,2)) {
                    $order->pay_change = 0;
                    // log::info('Si pago '.$order->pay_change);
                }else{
                    //log::info('cliente '.$totalCliente);
                    $order->pay_change = $pago - $totalCliente;
                    Log::info('El pago es: '.$pago);
                    Log::info('El totalC es: '.$totalCliente);
                    Log::info('Cambio es: '.$order->pay_change);
                }
               
                //log::info('pago '.gettype($pago));
                //log::info('totalOrden '.gettype($totalOrden));
                //log::info('costoEnvio: '.gettype($costoEnvio));
                //log::info('EL cambioCliente: '.gettype($cambioCliente));

                // $order->pay_change = $cambioCliente;
                // $order->pay_change = round(($order->pay_bill),2) - (round((floatval(substr($order->order_total, 1))),2) + round(($order->shipping_charge),1, PHP_ROUND_HALF_UP));
                //log::info(' $order->pay_change: '. $order);
                //Validando que el pay_bill no sea menor que el total a pagar
                if($order->pay_change < 0)
                    throw new Exception("Debe ingresar una cantidad igual o mayor a la del precio total");
            }elseif ($payment_method->payment_method_id == 3){
                 ///////////////Tigo Money
                $subtotal = substr($order->order_total,1);
                $order->billetera_user = $datos['datosCompra']['billetera_user'];
                $order->num_tigo_money = $datos['datosCompra']['num_tigo_money'];
                $tigo = DB::table('res_payment_methods')->where('payment_method_id', 3)->first();
                $tigo_money_charge=round((($subtotal + $order->shipping_charge) * $tigo->payment_method_charge),6);
                //aproximando  0.17575 a 0.18
                $tigo_money_charge= $tigo_money_charge+0;
                $cad = explode(".", $tigo_money_charge);
              
                // log::info('cad: '.$cad[1]);
                $rest= substr($cad[1], 2, 1);  // abcd
                if($rest<5 && $rest!=0 && $rest!=null) {
                   $tigo_money_charge=round(($tigo_money_charge+0.01),2);
                }else{
                    $tigo_money_charge=round(($tigo_money_charge),2);
                }                
                //log::info('rest: '.$rest);

                $order->tigo_money_charge = $tigo_money_charge;
                // log::info('Sin aprox. cargo tg money: '.($subtotal + $order->shipping_charge) * $tigo->payment_method_charge);
                // log::info('cargo tg money: '.$order->tigo_money_charge);
                ////////////////
            }

            $order->order_cod = $this->numeroOrden();
            $usuario = User::where('user_id', $user_id)->get(); 
            $datosUsuario = json_decode($usuario, true);
            //Log::info(' billetera_user: '. $order->billetera_user.' |-num_tigo_money: '. $order->num_tigo_money);

            ///Manejo Ordenes Android         
            if (isset($tarjetaNoEncript)) {
                $tarjetaNoEncript=1;
            } else {
                $tarjetaNoEncript=0;
            }
            
            ////////////////////banca
            if ($payment_method->payment_method_id == 2 && $datosUsuario[0]['sospecha']!=1 && $datos['datosCompra']['source_device']=='Android' && $tarjetaNoEncript!=1){
                //INICIO ********************************Cobro automatico API-BAC*****************************/
                //Objetivo: Realizar automaticamente el cobro de las ordenes al Banco
                //Parametros necesario para peticion Post
                //if( $order->credit_expmonth < 10)$order->credit_expmonth= '0'.$order->credit_expmonth;//agregando 0 al mes < 10 
                //$bank_option = DB::table('bank_option')->where('bank_id', 'bank01')->get();
                //$bank_option = BankOption::where('bank_id','bank01')->get(); 
                $order->credit_name = $datos['datosCompra']['credit_name'];
                $order->credit_card = $datos['datosCompra']['credit_card'];
                $order->credit_expmonth = $datos['datosCompra']['credit_expmonth'];
                $order->credit_expyear = $datos['datosCompra']['credit_expyear'];
                $order->secure_code = $datos['datosCompra']['secure_code'];
                $card = DB::table('res_payment_methods')->where('payment_method_id', 2)->first();
                $subtotal = substr($order->order_total,1);
                //$order->credit_charge = round((($subtotal + $order->shipping_charge) * $card->payment_method_charge),6);
                $credit_charge = round((($subtotal + $order->shipping_charge) * $card->payment_method_charge),6);
                ///////////////Tigo Money
            
                $credit_charge= $credit_charge+0;
                $cad = explode(".", $credit_charge);
              
                //log::info('cad: '.$cad[1]);
                $rest= substr($cad[1], 2, 1);  // abcd
                if ($rest!=null && $rest<5 && $rest!=0) {
                   $credit_charge=round(($credit_charge+0.01),2);
                   // log::info('SI:credit_charge: '.$credit_charge);
                }else{
                    $credit_charge=round(($credit_charge),2);
                    //log::info('NO:credit_charge: '.$credit_charge);
                }
                
                //log::info('rest: '.$rest);

                $order->credit_charge = $credit_charge;
                //log::info('Sin aprox. cargo credit_charge: '.round((($subtotal + $order->shipping_charge) * $card->payment_method_charge),6));
                //log::info('cargo credit_charge: '.$order->credit_charge);
                ////////////////
                //Obteniendo los parametros del banco desde la tabla pf.bank_option
                $bank_option = BankOption::findOrFail(1);
                //log::info("llave del banco: ".$bank_option->key_id_bank);
                $password = $bank_option->username_password;
                $username = $bank_option->username_bank;
                $type = $bank_option->type_bank;
                $key_BAC = $bank_option->key_bank;//Key privado de la cuenta bancaria, 
                $key_ID_BAC = $bank_option->key_id_bank;// Key publica
                //$hashEntradaBAC =''; //almacenara el hash de entrada 
                $UnixTime = time();
                $amount = round((floatval(substr($order->order_total, 1))),2) + round(($order->shipping_charge),2)+$order->credit_charge; //total
                $ccnumber = $order->credit_card = $datos['datosCompra']['credit_card']; //dummy visa
                $ccexp = $order->credit_expmonth.substr($order->credit_expyear,-2);// contruyendo mes/año con 2 digitos
                $cvv = $order->secure_code = $datos['datosCompra']['secure_code'];
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
                                        
                //$hashEntradaBAC= md5( $order->order_cod ."|". $amount ."|". $UnixTime ."|". $key_BAC); 
                // Log::info($hashEntradaBAC);
                // abrimos la sesión cURL
                $ch = curl_init();
                $timeout = 30; // set to zero for no timeout

                $data = "username=".$username."&"."password=".$password."&"."type=".$type."&"."amount=".$amount."&"."encrypted_payment=".$order->credit_card;                    
                Log::info('Parametros enviados: '.$data);
                    
                // definimos la URL a la que hacemos la petición
                curl_setopt($ch, CURLOPT_URL,$bank_url);
                // definimos el número de campos o parámetros que enviamos mediante POST
                curl_setopt($ch, CURLOPT_POST, 1);
                // definimos cada uno de los parámetros                
                curl_setopt($ch, CURLOPT_POSTFIELDS,$data);                 
                // recibimos la respuesta y la guardamos en una variable
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                //tiempos de espera de conexion
                curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout); 
                curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
                $remote_server_output = curl_exec ($ch);
                     
                // cerramos la sesión cURL
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
                        throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
                        break;
                    case 201:
                        throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
                        break;
                    case 202:
                        throw new Exception("Lo sentimos, Fondos insuficientes. Intente con otro método de pago.");
                        break;
                    case 203:
                        throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
                        break;
                    case 204:
                        throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
                        break;
                    case 220:
                        throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
                        break;
                    case 221:
                        throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
                        break;    
                    case 222:
                        throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
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
                    case 240:
                        throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
                        break;
                    case 250:

                        throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
                    break;
                    case 251:
                        $user = User::find($datos['datosCompra']['user_id']);
                        $user->sospecha=1; 
                        $user->save();
                        Log::info('Usuario a sospecha: '. $user->user_id );
                        throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
                        break; 
                    case 252:
                        $user = User::find($datos['datosCompra']['user_id']);
                        $user->sospecha=1; 
                        $user->save();
                        Log::info('Usuario a sospecha: '. $user->user_id );
                        throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
                        break;
                    case 253:
                        $user = User::find($datos['datosCompra']['user_id']);
                        $user->sospecha=1; 
                        $user->save();
                        Log::info('Usuario a sospecha: '. $user->user_id );
                        throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
                        break; 
                    case 260:
                        throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
                        break;       
                    case 261:
                        throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
                        break; 
                    case 262:
                        throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
                        break;
                    case 263:
                        throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
                        break;
                    case 264:
                        throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
                        break;
                    case 300:
                        throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
                        break; 
                    case 400:
                        throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
                        break; 
                    case 410:
                        throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
                        break;  
                    case 411:
                        throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
                        break;
                    case 420:
                        throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
                        break;                               
                    case 421:
                        throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
                        break;                               
                    case 430:
                        throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
                        break;                               
                    case 440:
                        throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
                        break;                               
                    case 441:
                        throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
                        break;                               
                    case 460:
                        throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
                        break;  
                    case 461:
                        throw new Exception("Lo sentimos, Su tarjeta fue rechazada por el Banco. Intente con otro método de pago.");
                        break;                                 
                    
                    default:
                        # sino es ninguno de los parametros actuales de BAC
                        throw new Exception("Error en la Transacción bancaria. Intente con otro método de pago.");
                        break;
                }

                /* Fin Control de flujo de la orden*/
                    //FIN    ********************************Cobro automatico API-BAC******************************/  
            }// if ($payment_method->payment_method_id == 2)
            elseif ($datosUsuario[0]['sospecha']==1){
                throw new Exception("Orden incobrable. Intente con otro método de pago.");
            }//fin else{
            elseif($payment_method->payment_method_id == 1) {
                $order->pay_bill = $datos['datosCompra']['pay_bill'];
                //log::info('pay_bill: '.$order->pay_bill);

                //log::info('round(($order->pay_bill),2): '.round(($order->pay_bill),2));
                $pago = round(($order->pay_bill),2);
                $totalOrden =round((substr($order->order_total, 1)),2);
                $costoEnvio = round($order->shipping_charge,2);
                $totalCliente = ($totalOrden + $costoEnvio);
                $cambioCliente = $pago - $totalCliente;
                if (round($totalCliente,2) == round($pago,2) ) {
                    $order->pay_change = 0;
                    // log::info('Si pago '.$order->pay_change);
                }else{
                    //log::info('cliente '.$totalCliente);
                    $order->pay_change = $pago - $totalCliente;
                }
               
                //log::info('pago '.gettype($pago));
                ///log::info('totalOrden '.gettype($totalOrden));
                //log::info('costoEnvio: '.gettype($costoEnvio));
                //log::info('EL cambioCliente: '.gettype($cambioCliente));

                // $order->pay_change = $cambioCliente;
                // $order->pay_change = round(($order->pay_bill),2) - (round((floatval(substr($order->order_total, 1))),2) + round(($order->shipping_charge),1, PHP_ROUND_HALF_UP));
                // log::info(' $order->pay_change: '. $order);
                if($order->pay_change < 0)
                    throw new Exception("Debe ingresar una cantidad igual o mayor a la del precio total");
            }elseif ($payment_method->payment_method_id == 3) {
                 ///////////////Tigo Money
                $subtotal = substr($order->order_total,1);
                $order->billetera_user = $datos['datosCompra']['billetera_user'];
                $order->num_tigo_money = $datos['datosCompra']['num_tigo_money'];
                $tigo = DB::table('res_payment_methods')->where('payment_method_id', 3)->first();
                $tigo_money_charge=round((($subtotal + $order->shipping_charge) * $tigo->payment_method_charge),6);
                //aproximando  0.17575 a 0.18
                $tigo_money_charge= $tigo_money_charge+0;
                $cad = explode(".", $tigo_money_charge);                          
                              
                // log::info('cad: '.$cad[1]);
                $rest= substr($cad[1], 2, 1);  // abcd
                if($rest<5 && $rest!=0 && $rest!=null){
                   $tigo_money_charge=round(($tigo_money_charge+0.01),2);
                }else{
                    $tigo_money_charge=round(($tigo_money_charge),2);
                }
                        
                //  log::info('rest: '.$rest);
                $order->tigo_money_charge = $tigo_money_charge;
                //log::info('Sin aprox. cargo tg money: '.($subtotal + $order->shipping_charge) * $tigo->payment_method_charge);
                //log::info('cargo tg money: '.$order->tigo_money_charge);
                ////////////////
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

            foreach ($datos['products'] as $product) {

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
            }

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
    public function show($order_id) {
        $input = Input::all();
        try {
            $statusCode = 200;
            $order = Order::findOrFail($order_id);
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
                "data" => $order
            );
        }catch (Exception $e){
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

    public function shipping_charge($datos) {
        //$input = Input::all();
        $input = explode(",", $datos);
        Log::info($input[0]); // porción1
        Log::info($input[1]); // porción2
        try {
            $statusCode = 200;

            $zone = DB::table('restaurants_zones')->where('zone_id', $input[0])->where('restaurant_id', $input[1])->first();

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
