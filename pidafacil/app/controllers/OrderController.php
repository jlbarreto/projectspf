<?php

class OrderController extends \BaseController {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        // $orders = User::find(Auth::id())->orders;
        // $orders = Order::users()

        $date = new DateTime();
        $fecha = $date->format('Y-m-d H:i:s');

        $promociones = Product::where('activate',1)
                            ->where('promotion',1)
                            ->where('res_products.start_date', '<=', $fecha)->where('res_products.end_date', '>=', $fecha)
                            ->count();

        $orders = Order::join('req_order_ratings', function($join) {
                    $join->on('req_orders.order_id', '=', 'req_order_ratings.order_id')
                    ->where('req_order_ratings.user_id', '=', Auth::id());
                })
                ->with('statusLogs')
                ->orderBy('created_at', 'desc')
                ->groupBy('req_orders.order_id')
                ->get();

        return View::make('web.user_orders')
                ->with('promociones', $promociones)
                ->with('orders', $orders);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
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
            $source .= 'abcdefghjkmnpqrstuvwxyz';
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

    public function store(){
        
        try{

            $rules = array(
                'service_type_id' => 'required|numeric',
                'payment_method_id' => 'required|numeric'
            );
            $display = array(
                'service_type_id' => 'Tipo de servicio',
                'payment_method_id' => 'M&eacute;todo de pago'
            );
            
            //Rules before defined
            $validator = Validator::make(Input::all(), $rules);
            
            //Field name translation
            $validator->setAttributeNames($display);

            if($validator->fails()){
                return Redirect::to('cart/checkout')
                    ->withErrors($validator)
                    ->withInput();
            }else{
                $irules = array();
                $idisplay = array();

                $st_id = Input::get('service_type_id');
                
                if($st_id == 1){
                    $irules = array_add($irules, 'address_id', 'required|numeric');
                    $idisplay = array_add($idisplay, 'address_id', 'Direcci&oacute;n de env&iacute;o');
                }elseif ($st_id == 2){
                    $irules = array_add($irules, 'restaurant_address', 'required|numeric');
                    $idisplay = array_add($idisplay, 'restaurant_address', 'Direcci&oacute;n de sucursal');
                }

                $pm_id = Input::get('payment_method_id');
                $valorccv = Input::get('nombre_tarjeta');
                $mes = Input::get('month');
                $año = Input::get('year');
                $mesActual = date("n");
                $añoActual = date("Y");
                #$test=1;
                
                if($pm_id == 1){
                    $irules = array_add($irules, 'cash', 'required');
                    $idisplay = array_add($idisplay, 'cash', 'Efectivo');
                }elseif ($pm_id == 2){
                    $irules = array_add($irules, 'user_credit', 'required|min:6');
                    $irules = array_add($irules, 'credit_card', 'required|luhn');
                    
                    if($mes < $mesActual && $año == $añoActual){
                        $irules = array_add($irules, 'month', 'required|date');
                        $irules = array_add($irules, 'year', 'required|date');
                    }else{
                        $irules = array_add($irules, 'month', 'required|numeric|min:1');
                    }
                    
                    $irules = array_add($irules, 'year', 'required|numeric|min:1');
                    
                    if($valorccv == 4){
                        $irules = array_add($irules, 'secure_code', 'required|min:4');
                    }else{
                        $irules = array_add($irules, 'secure_code', 'required|min:3');
                    }

                    $idisplay = array_add($idisplay, 'user_credit', 'Nombre del titular');
                    $idisplay = array_add($idisplay, 'credit_card', 'N&uacute;mero de la tarjeta');
                    $idisplay = array_add($idisplay, 'month', 'Mes de expiraci&oacute;n');
                    $idisplay = array_add($idisplay, 'year', 'A&ntilde;o de expiraci&oacute;n');
                    $idisplay = array_add($idisplay, 'secure_code', 'C&oacute;digo de seguridad');
                }

                //Rules before defined
                $ivalidator = Validator::make(Input::all(), $irules);
                
                //Field name translation
                $ivalidator->setAttributeNames($idisplay);

                if($ivalidator->fails()){
                    $messages = $ivalidator->messages();
                    return Redirect::to('cart/checkout')
                        ->withErrors($ivalidator)
                        ->withInput(Input::except('credit_card', 'secure_code'));
                }else{
                    // return Response::json(array('message'=>'Guardar orden'), 200);
                    $idusuario = Auth::id();
                    
                    if (Session::has('cart')){
                        $cart = Session::get('cart');
                    }else{
                        return Redirect::to('cart');
                    }

                    /* ############################################################## */
                    if(is_array($cart)){
                        foreach($cart as $req_restaurant => $req_order){

                            //------------- Se comienza la orden -------------------
                            if(!empty($idusuario) && $req_restaurant != 'name' && is_numeric($req_restaurant)){
                                //verificamos si corresponde al id de restaurante
                                //realizamos conteo de direcciones
                                $nuevaorden = new Order; //creamos una nueva orden
                                $service_type_id = Input::get('service_type_id');

                                /*CODIGO PARA INSERTAR NOMBRE Y TELEFONO DEL USUARIO A LA ORDEN*/
                                $nuevaorden->customer = Input::get('nombre_user');
                                $nuevaorden->customer_phone = Input::get('telefono_user');

                                $idRes = DB::Table('res_restaurants')
                                        ->select('parent_restaurant_id')
                                        ->where('restaurant_id', $req_restaurant)
                                        ->get();

                                $service_type = ServiceType::findOrFail($service_type_id);
                                $nuevaorden->service_type_id = $service_type->service_type_id;

                                if($service_type->service_type_id == 1 || $service_type->service_type_id == 3) {

                                    $nuevaorden->restaurant_id = $idRes[0]->parent_restaurant_id;
                                    $addresses = Address::where('user_id', $idusuario)->get();
                                    
                                    if(count($addresses) == 1){
                                        //si existe solo una dirección se trabajará con ella
                                        $direccion = $addresses[0];
                                    }else{
                                        // Si existe más de una dirección se espera parámetro para decidir con cual trabajará
                                        $direccion = (Input::has('address_id'))? Address::where('address_id', Input::get('address_id'))->first():$addresses[0];
                                    }

                                    $nuevaorden->address = $direccion->address_name . ' - ' . $direccion->address_1 .
                                        (!empty($direccion->address_2) ? ', ' . $direccion->address_2 : '');

                                    $nuevaorden->address_id=$direccion->address_id;

                                    //Definiendo costo de envío
                                    
                                    //AQUI DEBO VERIFICAR EL FREE SHIPPING**********
                                    if($nuevaorden->service_type_id == 1){
                                        $restaurant = Restaurant::findOrFail($nuevaorden->restaurant_id);
                                        $nuevaorden->shipping_charge = $restaurant->shipping_cost;                                        
                                    }else{
                                        $zone = DB::table('restaurants_zones')
                                            ->where('zone_id', $direccion->zone_id)->where('restaurant_id', $nuevaorden->restaurant_id)->first();

                                        /**CALCULO EL FREE SHIPPING SI APLICA**/
                                        $free = DB::SELECT('
                                                    SELECT * FROM pf.free_shipping_restaurants
                                                    WHERE restaurant_id = "'.$nuevaorden->restaurant_id.'"
                                                ');

                                        $total = Input::get('total_previo');                                        
                                        Log::info('Total controller2: '.$total);
                                        $contArr = count($free);

                                        if($contArr > 0) {
                                            foreach ($free as $value) {
                                                $arreglo = $value;       
                                            }
                                            
                                            Log::info("Monto minimo2: ".$arreglo->monto_minimo);
                                            
                                            $conteo = count($arreglo);

                                            if($conteo > 0 && $total >= $arreglo->monto_minimo){
                                                Log::info("entra al if conteo");
                                                $nuevaorden->shipping_charge = 0.0;
                                            }else{
                                                Log::info("NO entra al if conteo");
                                                $nuevaorden->shipping_charge = ($zone==NULL)? 0: $zone->shipping_charge;
                                            }
                                        }else{
                                            Log::info("NO entra al if conteo");
                                            $nuevaorden->shipping_charge = ($zone==NULL)? 0: $zone->shipping_charge;
                                        }
                                    }

                                    //FIN FREE SHIPPING*********************
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
                                if(is_array($req_order)){
                                    $totalF = 0;
                                    foreach ($req_order as $key => $req_product) {
                                        $product = Product::findOrFail($req_product['product_id']);
                                        $nuevo_total = ($product->value * $req_product['quantity']);
                                        $totalF += $nuevo_total;
                                    }
                                    $nuevaorden->order_total = $totalF;
                                }

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
                                        $nuevaorden->source_device = 'Web';
                                        $nuevaorden->save(); //Se hace update de precio final

                                        if (Session::has('bin_id'))
                                        {
                                            $bin_id = Session::get('bin_id');
                                            $bin = TransactionBin::find($bin_id);
                                            $bin->req_order_id = $nuevaorden->order_id;
                                            $bin->save();
                                            Session::forget('bin_id');

                                        }
                                        $config = DB::table('config_bins')->get();
                                        $porcentaje = $config[0]->porcentaje;
                                        $activate = $config[0]->activate;  

                                        if ($activate == 1) {
                                            $nuevaorden->order_total = $nuevaorden->order_total -round(($nuevaorden->order_total * $porcentaje/100),2);
                                            $nuevaorden->shipping_charge = $nuevaorden->shipping_charge - round(($nuevaorden->shipping_charge * $porcentaje/100),2);
                                            $nuevaorden->credit_charge =$nuevaorden->credit_charge- round(($nuevaorden->credit_charge * $porcentaje/100),2);

                                            $nuevaorden->save();

                                        }                    

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
                                        //$order_usr->rating_date = "0000-00-00 00:00:00";
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
                                        //$order_usr->rating_date = "0000-00-00 00:00:00";
                                        $order_usr->save();
                                    }elseif($payment_method->payment_method_id == 3){//COMIENZA CODIGO PARA TIGO MONEY
                                        $nuevaorden->order_cod = $this->numeroOrden();
                                        $nuevaorden->num_tigo_money = Input::get('num_debitar');
                                        $nuevaorden->billetera_user = Input::get('billetera');
                                        #$nuevaorden->tigo_money_charge = Input::get('cargo_uso_tigo');

                                        $tigo = DB::table('res_payment_methods')->where('payment_method_id', 3)->first();
                                        $tigo_charge = round((($totalF + $nuevaorden->shipping_charge) * $tigo->payment_method_charge),2);
                                        
                                        /*cargo de tigo*/
                                        if($tigo_charge != 0){
                                            $tigo_charge= $tigo_charge+0;
                                            $cad = explode(".", $tigo_charge);

                                            //log::info('cad: '.$cad[1]);
                                            $rest= substr($cad[1], 2, 1); // abcd
                                            //log::info('rest TM: '.$rest);

                                            if($rest != null && $rest < 5) {
                                                $tigo_charge=round(($tigo_charge + 0.01),2);
                                                log::info('SI:tigo_charge: '.$tigo_charge);
                                            }else{
                                                $tigo_charge=round(($tigo_charge+0.01),2);
                                                log::info('NO:tigo_charge: '.$tigo_charge);
                                            }
                                        }else{
                                            $tigo_charge = 0;                                            
                                        }

                                        $nuevaorden->tigo_money_charge = $tigo_charge;
                                        log::info('Sin aprox. cargo tigo_charge: '.round((($totalF + $nuevaorden->shipping_charge) * $tigo->payment_method_charge),6));
                                        log::info('cargo tigo_charge: '.$nuevaorden->tigo_money_charge);

                                        /*FIN TIGO*/
                                        $nuevaorden->source_device = 'Web';
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
                                        //$order_usr->rating_date = "0000-00-00 00:00:00";
                                        $order_usr->save();
                                    }else
                                        return 'No existe tipo de pago';
                                }else
                                    return 'No existe tipo de pago';
                            }

                            if(is_array($req_order)){
                                foreach ($req_order as $key => $req_product){
                                    //Se comienza el detail de de la orden
                                    $detalle_producto = new OrderDetail;
                                    $detalle_producto->order_id = $nuevaorden->order_id;
                                    $detalle_producto->product_id = $req_product['product_id'];
                                    $detalle_producto->quantity = $req_product['quantity'];
                                    $detalle_producto->product = $req_product['product'];
                                    $detalle_producto->unit_price = $req_product['unit_price'];
                                    $detalle_producto->total_price = $req_product['total_price'];
                                    $detalle_producto->comment = $req_product['comment'];
                                    $detalle_producto->save();

                                    /*$product = Product::findOrFail($detalle_producto->product_id);
                                    $nuevo_total = ($product->value * $detalle_producto->quantity) + $nuevaorden->order_total;
                                    $nuevaorden->order_total = $nuevo_total;
                                    $nuevaorden->order_cod = $this->numeroOrden();*/
                                    
                                    #$nuevaorden->save(); //Se hace update de precio final
                                    
                                    if(!empty($req_product['ingredients'])){
                                        foreach ($req_product['ingredients'] as $ingrediente){
                                            //Verificando los ingredientes de la orden
                                            $detalle_ingrediente = new OrderDetailProductIngredient;
                                            $detalle_ingrediente->order_det_id = $detalle_producto->order_det_id;
                                            $ingredient = Ingredient::findOrFail($ingrediente['ingredient_id']);
                                            $detalle_ingrediente->ingredient_id = $ingredient->ingredient_id;
                                            $detalle_ingrediente->ingredient = $ingredient->ingredient;
                                            
                                            if($ingrediente['active'] == 1){
                                                $detalle_ingrediente->remove = 0;
                                            }else{
                                                $detalle_ingrediente->remove = 1;
                                            }

                                            $detalle_ingrediente->save();
                                        }
                                    }
                                    
                                    if(!empty($req_product['conditions'])){
                                        //verificando las condiciones que hay en el producto
                                        foreach ($req_product['conditions'] as $condition) {
                                            $detalle_opcion = new OrderDetailProductCondition;
                                            $detalle_opcion->order_det_id = $detalle_producto->order_det_id;
                                            $detalle_opcion->condition_id = $condition['condition_id'];
                                            $detalle_opcion->condition = $condition['condition_condition'];
                                            $detalle_opcion->condition_option_id = $condition['condition_option_id'];
                                            $detalle_opcion->condition_option = $condition['condition_option'];
                                            $detalle_opcion->save();
                                        }
                                    }
                                }

                                if($nuevaorden->pay_bill > 0){
                                    $nuevaorden->pay_change = $nuevaorden->pay_bill - $nuevaorden->order_total - $nuevaorden->shipping_charge;
                                    $nuevaorden->save();
                                }
                            }
                        }
                    }

                    $response['status'] = true;
                    $response['data'] = $nuevaorden;
                    Session::forget('cart');
                    Session::forget('total_order');
                    Session::forget('cart2');

                    return Redirect::to('user/orders');
                    /* ############################################################## */
                }
            }
        }catch(\Illuminate\Database\QueryException $e){
            $error = $e->getMessage();
            $cadena = explode('@', $e);

            Log::info($error);

            return Redirect::to('cart/checkout')
                ->withErrors($cadena[1]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id) {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function lista($restslug){
        if ($restslug == 'all') {
            return Order::orderBy('created_at', 'desc')->get();
        }else{
            $restaurante = Restaurant::where('slug', $restslug)->firstOrFail();
            return $restaurante->orders()->orderBy('created_at', 'desc')->get();
        }
    }

    public function orden($restslug, $id) {
        $restaurant = Restaurant::where('slug', $restslug)->firstOrFail();
        $orden = Order::where('restaurant_id', $restaurant->restaurant_id)->where('order_id', $id)->firstOrFail();
        $response['order'] = $orden;
        $response['servicetype'] = $orden->serviceType()->get();
        $response['paymentMethod'] = $orden->paymentMethod()->get();
        $response['orderStatus'] = $orden->orderStatus()->get();
        return $orden->products()->get();
        return $response;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update() {
        $input = Input::all();

        $order = Order::find($input['order_id'])->firstOrFail();
        $order->service_type_id = $input['service_type_id']; //NULL by default
        $order->address_id = $input['address_id']; //NULL by default
        $order->payment_method_id = $input['payment_method_id']; //NULL by default
        $order->order_status_id = 2;

        $order->save();

        $response['status'] = true;
        $response['data'] = $order;

        return $response;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id) {
        //
    }

    /**
     * Show completed orders by user.
     *
     * @return Response
     */
    public function repeat() {
        $input = Input::all();
        if (!empty($input['oid']) && $input['oid'] > 0) {
            $oid = $input['oid'];
            $order = Order::findOrFail($oid);
            $user = $order->users->first();
            if ($user->user_id == Auth::id()) {
                Session::forget('cart');
                Session::forget('cart2');
                $cart = array();
                $restaurant_id = $order->restaurant_id;
                $cart[$restaurant_id] = array();
                $cart['name'] = $order->restaurant->name;

                $products = $order->products;
                $optids = '';
                $ingids = '';
                foreach ($products as $key => $value) {
                    $prd = $value->getProduct;
                    $cnds = $value->conditions;
                    $ings = $value->ingredients;

                    if ($cnds->count() > 0) {
                        foreach ($cnds as $ky => $val) {
                            $optids .= $val->condition_option_id;
                            $pconditions[] = array(
                                "condition_id" => $val->condition_id,
                                "condition_condition" => $val->condition,
                                "condition_option_id" => $val->condition_option_id,
                                "condition_option" => $val->condition_option
                            );
                        }
                    } else {
                        $pconditions = NULL;
                    }

                    if ($ings->count() > 0) {
                        foreach ($ings as $k => $v) {
                            $ingids .= $v->ingredient_id;

                            if ($v->remove == 1) {
                                $active = 0;
                            } elseif ($v->remove == 0) {
                                $active = 1;
                            }

                            $pingredients[] = array(
                                "ingredient_id" => $v->ingredient_id,
                                "ingredient" => $v->ingredient,
                                "active" => $active
                            );
                        }
                    } else {
                        $pingredients = NULL;
                    }

                    $pkey = $prd->product_id . $optids . $ingids;

                    $cart_row = array(
                        "product_id" => $prd->product_id,
                        "product" => $prd->product,
                        "description" => $prd->description,
                        "conditions" => $pconditions,
                        "ingredients" => $pingredients,
                        "quantity" => $value->quantity,
                        "comment" => $value->comment,
                        "unit_price" => $prd->value,
                        "total_price" => round($value->quantity * $prd->value, 2)
                    );
                    $cart[$restaurant_id] = array_add($cart[$restaurant_id], $pkey, $cart_row);
                }
                //$response = $cart;
                $cant = $value->quantity; 
                Session::put('cart', $cart);
                Session::put('cart2', $cant);
                /* ##########################################################################
                  $product = Product::where('product_id',$input['product_id'])->with('section')->first();
                  if(count($product) > 0){
                  $restaurant = Restaurant::find($product->section->restaurant_id);
                  $restaurant_id = $restaurant->restaurant_id;

                  $optids = ''; $ingids = '';

                  $pconditions = array();
                  if(isset($input['condition']) && count($input['condition']) > 0){
                  foreach ($input['condition'] as $key => $value) {
                  $condition    = Condition::find($key);
                  $option   = ConditionOption::find($value);
                  $optids .= $option->condition_option_id;
                  $pconditions[] = array(
                  "condition_id"            => $condition->condition_id,
                  "condition_condition"     => $condition->condition,
                  "condition_option_id"     => $option->condition_option_id,
                  "condition_option"        => $option->condition_option
                  );
                  }
                  }

                  $pingredients = array();
                  if(isset($input['ingredients']) && count($input['ingredients']) > 0){
                  foreach ($input['ingredients'] as $key => $value) {
                  $ingredient = Ingredient::find($key);

                  if($value == 0){
                  $ingids .= $ingredient->ingredient_id;
                  }

                  $pingredients[] = array(
                  "ingredient_id" => $ingredient->ingredient_id,
                  "ingredient"  => $ingredient->ingredient,
                  "active"      => $value
                  );
                  }
                  }
                  $key = $product->product_id . $optids . $ingids;

                  $cart_row = array(
                  "product_id"      => $product->product_id,
                  "product"         => $product->product,
                  "description" => $product->description,
                  "conditions"  => $pconditions,
                  "ingredients" => $pingredients,
                  "quantity"        => $input['quantity'],
                  "comment"     => $input['comment'],
                  "unit_price"      => $product->value,
                  "total_price"     => round($input['quantity']*$product->value, 2)
                  );

                  $cart[$restaurant_id] = array_add($cart[$restaurant_id], $key, $cart_row);
                  }
                  /* ########################################################################## */
            } else {
                $response = array('message' => 'User not allowed');
            }
        } else {
            $response = array('message' => 'Order not allowed');
        }

        // $orders = Order::users()

        /* $orders = DB::table('req_order_status_logs')
          ->select(DB::raw('MAX(req_order_status_logs.order_status_id) as ultimo, req_orders.order_id'))
          ->leftJoin('req_orders', 'req_order_status_logs.order_id', '=', 'req_orders.order_id')
          ->leftJoin('req_order_ratings', 'req_orders.order_id', '=', 'req_order_ratings.order_id')
          ->where('req_order_ratings.user_id', '=', Auth::id())
          ->whereNotNull('req_orders.address')
          ->having('ultimo', '=', 5)
          ->groupBy('req_orders.order_id')
          ->get();
         */

        // return Response::json($response, 200);
        return Redirect::action('CartController@index');
    }

    /**
     * Calcula el cargo de precio
     * @return type
     */
    public function shipping_charge() {
        $input = Input::all();
        try {

            $free = DB::SELECT('
                        SELECT * FROM pf.free_shipping_restaurants
                        WHERE restaurant_id = "'.$input['restaurant_id'].'"
                    ');

            $total = $input['subtotal'];
            Log::info($total);
            $contArr = count($free);

            if($contArr > 0) {
                foreach ($free as $value) {
                    $arreglo = $value;       
                }

                Log::info($arreglo->monto_minimo);
            
                $conteo = count($arreglo);

                if($conteo > 0 && $total >= $arreglo->monto_minimo){
                    $statusCode = 200;

                    $response = array(
                        "status" => true,
                        "type" => 'free',
                        "data" => $arreglo
                    );                
                }else{
                    $statusCode = 200;
                    $address = Address::findOrFail($input['address_id']);

                    $response = array(
                        "status" => true,
                        "type" => 'no_free',
                        "data" => $this->getShippingCharge($input['restaurant_id'], $address->zone_id)
                    );
                }
            }else{
                $statusCode = 200;

                $address = Address::findOrFail($input['address_id']);

                $response = array(
                    "status" => true,
                    "type" => 'no_free',
                    "data" => $this->getShippingCharge($input['restaurant_id'], $address->zone_id)
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

    public function getUserData(){
        #$id_user = Session::get('usuario_id');
        $input = Input::all();

        $usuario = DB::table('pf.com_users')
                        ->select('name','last_name','phone')
                        ->where('user_id', Auth::user()->user_id)
                        ->get();

        return Response::json($usuario);
    }

    private function getShippingCharge($restaurant_id, $zone_id) {
        return DB::table('restaurants_zones')->where('zone_id', $zone_id)
                ->where('restaurant_id', $restaurant_id)->first();
    }

    public function getTimeEst(){

        $idD = Input::get('direccion');

        $diaActual = date('w');
        $horaActual = date("H:i:s");
        #$horaActual = '23:00:00';
        $newDay = $diaActual + 1;

        $result = DB::table('pf.com_zones_traffic')
                    ->join('diner_addresses', 'com_zones_traffic.zone_id', '=', 'diner_addresses.zone_id')
                    ->where('com_zones_traffic.day_id',$newDay)
                    ->where('com_zones_traffic.traffic_beginning','<=',$horaActual)
                    ->where('com_zones_traffic.traffic_end','>=',$horaActual)
                    ->where('diner_addresses.address_id',$idD)
                    ->select('com_zones_traffic.prom_time')
                    ->get();

        /*$result = DB::select('
            SELECT tra.prom_time FROM pf.com_zones_traffic as tra 
            inner join diner_addresses as da ON tra.zone_id = da.zone_id
            where day_id = "'.$diaActual.'" and 
            ("'.$horaActual.'" >= tra.traffic_beginning and "'.$horaActual.'" <= tra.traffic_end)
            and da.address_id = "'.$idD.'"
        ');*/
        return Response::json($result);
    }
}
