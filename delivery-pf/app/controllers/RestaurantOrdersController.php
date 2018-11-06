<?php

class RestaurantOrdersController extends \BaseController {

  public function __construct(EmailController $emails)
  {
    $this->email = $emails;
  }
  /**
  * Display the specified resource.
  * GET /restaurantorders/{id}
  *
  * @param  int  $id
  * @return Response
  */
  public function show($id){
    $date = new DateTime();
    $fecha = $date->format('Y-m-d');

    $res = Restaurant::get();
    $restaurant = Restaurant::select('restaurant_id')->where('orders_allocator_id', $id)->get();
    $restaruantsHijos = Restaurant::where('parent_restaurant_id', $id)->get();

    $limit = 0;
    $ids = array();

    for ($i = 0; $i < count($restaurant); $i++){
      $ids[$i] = $restaurant[$i]->restaurant_id;
    }
    if (count($ids) > 0) {
      $limit = Order::where(function ($query) use ($ids, $id) {
        $query->where(function ($query) use ($ids) {
          for ($i = 0; $i < count($ids); $i++) {
            $query->orWhere('restaurant_id', $ids[$i]);
          }
          $query->where(function ($query) {
            $query->orWhere('service_type_id', 1)
            ->orWhere('service_type_id', 3);
          });
        })->orWhere(function ($query) use ($id) {
          $query->where('service_type_id', 2)
          ->where('restaurant_id', $id);
        });
      })->whereNotNull('address')->count();
    } else {
      $limit = Order::where(function ($query) use ($ids, $id) {
      })->orWhere(function ($query) use ($id) {
        $query->where('service_type_id', 2)
        ->where('restaurant_id', $id);
      })->whereNotNull('address')->count();
    }

    $res = Restaurant::orderBy('name')->get();
    $restaurantes = array();

    foreach ($res as $r) {
      $restaurantes[$r->restaurant_id] = array('name' => $r->name, "parent" => $r->parent_restaurant_id,'sonido'=>$r->notification_sound);
    }

    $pending = array('fillter' => 0, 'delivery' => 0, 'pickup' => 0);
    $registered = array('fillter' => 0, 'delivery' => 0, 'pickup' => 0);
    $accepted = array('fillter' => 0, 'delivery' => 0, 'pickup' => 0);
    $dispatched = array('fillter' => 0, 'delivery' => 0, 'pickup' => 0);
    $delivered = array('fillter' => 0, 'delivery' => 0, 'pickup' => 0);
    $cancelled = array('fillter' => 0, 'delivery' => 0, 'pickup' => 0);
    $rejected = array('fillter' => 0, 'delivery' => 0, 'pickup' => 0);
    $uncollectible = array('fillter' => 0, 'delivery' => 0, 'pickup' => 0);
    $unassigned = array('fillter'=>0,'delivery'=>0,'pickup'=>0);
    $orders = "";

    $condi = "";

    if ($limit > 0) {

      $delivery = 0;
      $pickup = 0;

      if (isset($_GET['busqueda'])) {
        $userID = DB::table("com_users")
        ->select('user_id')
        ->orWhere('name', 'LIKE', '%' . Input::get("busqueda") . '%')
        ->orWhere('last_name', 'LIKE', '%' . Input::get("busqueda") . '%')
        ->orWhere('phone', Input::get("busqueda"))
        ->get();

        $orderRating = 0;

        if (count($userID) > 0) {
          $orderRating = DB::table("req_order_ratings")
          ->select('order_id')
          ->where(function ($query) use ($userID) {
            foreach ($userID as $idU) {
              $query->orWhere('user_id', $idU->user_id);
            }
          })
          ->get();
        }
        if (count($ids) > 0) {
          $orders = Order::where(function ($query) use ($ids, $id) {
            $query->where(function ($query) use ($ids, $id) {
              for ($i = 0; $i < count($ids); $i++) {
                $query->orWhere('restaurant_id', $ids[$i]);
              }
            })->where(function ($query) {
              $query->orWhere('service_type_id', 1)
              ->orWhere('service_type_id', 3);
              //->where('viewed_pidafacil',1);
            })->orWhere(function ($query) use ($id) {
              $query->where('service_type_id', 2)
              ->where('restaurant_id', $id);
            });
          })
          ->whereNotNull('address')
          ->where('created_at','LIKE','%'.$fecha.'%')
          ->with('statusLogs')->with('serviceType')->with('paymentMethod')
          ->with('products')->with('users')
          ->orderBy('req_orders.created_at', 'desc')->get();
        }else{
          $orders = Order::where(function ($query) use ($ids, $id) {
          })->orWhere(function ($query) use ($id) {
            $query->where('service_type_id', 2)
            ->where('restaurant_id', $id);
          })
          ->whereNotNull('address')
          ->where('created_at','LIKE','%'.$fecha.'%')
          ->with('statusLogs')->with('serviceType')->with('paymentMethod')
          ->with('products')->with('users')
          ->orderBy('req_orders.created_at', 'desc')->get();
        }
        /*Aqui hay que revisar las consultas para mostrar todos los pedidos*/
      }else{
        $role = DB::table('assigned_roles')
          ->select('role_id')
          ->where('user_id', Auth::user()->user_id)
          ->get();

        if($role[0]->role_id == '1'){
          if (count($ids) > 0) {
            $orders = Order::where(function ($query) use ($ids, $id){
              $query->where(function ($query) use ($ids, $id){
                for ($i = 0; $i < count($ids); $i++) {
                  $query->orWhere('restaurant_id', $ids[$i]);
                }
              })->where(function ($query){
                $query->orWhere('service_type_id', 1)
                ->orWhere('service_type_id', 3);
                //->where('viewed_pidafacil',1);
              })->orWhere(function ($query) use ($id){
                $query->where('service_type_id', 2)
                ->where('restaurant_id', $id);
              });
            })
            //->where('viewed_pidafacil',1)
            ->whereNotNull('address')
            ->where('created_at','LIKE','%'.$fecha.'%')
            ->with('statusLogs')->with('serviceType')->with('paymentMethod')
            ->with('products')->with('users')
            ->orderBy('req_orders.created_at', 'desc')->get();
          }else{
            $orders = Order::where(function ($query) use ($ids, $id){
            })->orWhere(function ($query) use ($id){
              $query->where('service_type_id', 2)
              ->where('restaurant_id', $id);
            })
            ->whereNotNull('address')
            ->with('statusLogs')->with('serviceType')->with('paymentMethod')
            ->with('products')->with('users')
            ->orderBy('req_orders.created_at', 'desc')->get();
          }
        }elseif($role[0]->role_id != '1'){
          if (count($ids) > 0){
            $orders = Order::where(function ($query) use ($ids, $id){
              $query->where(function ($query) use ($ids, $id) {
                for ($i = 0; $i < count($ids); $i++){
                  $query->orWhere('restaurant_id', $ids[$i]);
                }
              })->where(function ($query){
                $query->orWhere('service_type_id', 1)
                ->orWhere('service_type_id', 3)
                ->where('viewed_pidafacil',1);
              })->orWhere(function ($query) use ($id){
                $query->where('service_type_id', 2)
                ->where('restaurant_id', $id);
              });
            })
            //->where('viewed_pidafacil',1)
            ->whereNotNull('address')
            ->where('created_at','LIKE','%'.$fecha.'%')
            ->with('statusLogs')->with('serviceType')->with('paymentMethod')
            ->with('products')->with('users')
            ->orderBy('req_orders.created_at', 'desc')->get();
          } else {
            $orders = Order::where(function ($query) use ($ids, $id){
            })->orWhere(function ($query) use ($id){
              $query->where('service_type_id', 2)
              ->where('restaurant_id', $id);
            })
            ->whereNotNull('address')
            ->with('statusLogs')->with('serviceType')->with('paymentMethod')
            ->with('products')->with('users')
            ->orderBy('req_orders.created_at', 'desc')->get();
          }
        }
      }
      if(Restaurant::where('parent_restaurant_id', $id)->get()){
        $sucursales = Restaurant::where('parent_restaurant_id', $id)->get();
      }
      $condi = DB::table("res_conditions")->get();
      $stats = $this->stats($orders, $ids);
    }else{
      $orders = null;
      $stats = null;
    }

    //Verifica si el restaurante tiene mas de un despachador/orders_allocator_id entre sus sucursales.
    $MasDeUnDespacho = false;
    if(isset($restaruantsHijos[0])){
      $alocatorId1 = $restaruantsHijos[0]->orders_allocator_id;

      if(count($restaruantsHijos) > 0){
        foreach ($restaruantsHijos as $key => $value){
          if ($alocatorId1 != $value->orders_allocator_id){
            $MasDeUnDespacho = true;
            break;
          }
        }
      }
    }

    $zonas = Zone::select('zone_id', DB::raw('CONCAT(zone, " - $", price) AS name_zona'))
          ->join('pf.com_shipping_prices', 'pf.com_zones.shipping_price_id', '=', 'pf.com_shipping_prices.shipping_price_id')
          ->lists('name_zona', 'zone_id');
        
    $combobox = array('none' => "-- Seleccione una zona") + $zonas;
    $selected = array();

    if(empty($sucursales)){
      return View::make('web.delivery_pidafacil', compact('combobox', 'selected'))
      ->with('orders', $orders)
      ->with('stats', $stats)
      ->with('condi',$condi)
      ->with('restaurantes', $restaurantes)
      ->with('MasDeUnDespacho',$MasDeUnDespacho)
      ->with('id',$id)
      ->with('res', $res);
    }else{
      return View::make('web.delivery_pidafacil', compact('combobox', 'selected'))
      ->with('orders', $orders)
      ->with('stats', $stats)
      ->with('condi',$condi)
      ->with('sucursales',$sucursales)
      ->with('restaurantes', $restaurantes)
      ->with('MasDeUnDespacho',$MasDeUnDespacho)
      ->with('id',$id)
      ->with('res', $res);
    }
  }

  public function stats($orders, $res_id){
    $pending = array('fillter'=>0,'delivery'=>0,'pickup'=>0,'pidafacil'=>0);
    $registered = array('fillter'=>0,'delivery'=>0,'pickup'=>0,'pidafacil'=>0);
    $accepted = array('fillter'=>0,'delivery'=>0,'pickup'=>0,'pidafacil'=>0);
    $dispatched = array('fillter'=>0,'delivery'=>0,'pickup'=>0,'pidafacil'=>0);
    $delivered = array('fillter'=>0,'delivery'=>0,'pickup'=>0,'pidafacil'=>0);
    $cancelled = array('fillter'=>0,'delivery'=>0,'pickup'=>0,'pidafacil'=>0);
    $rejected = array('fillter'=>0,'delivery'=>0,'pickup'=>0,'pidafacil'=>0);
    $uncollectible = array('fillter'=>0,'delivery'=>0,'pickup'=>0,'pidafacil'=>0);
    $unassigned = array('fillter'=>0,'delivery'=>0,'pickup'=>0,'pidafacil'=>0);

    if($res_id == 0){
      $results = DB::select('
      SELECT MAX(req_order_status_logs.order_status_id) as ultimo, req_orders.order_id,req_orders.viewed_pidafacil,req_orders.service_type_id as type_id
      FROM req_order_status_logs
      LEFT JOIN req_orders ON req_order_status_logs.order_id = req_orders.order_id
      WHERE  req_orders.service_type_id = 3 AND NOT ISNULL(req_orders.address)
      GROUP BY req_orders.order_id');

      foreach ($results as $value){
        if (($value->ultimo == 1)){
          $pending['fillter'] += 1;
        }

        switch($value->ultimo){
          /*case 1: $pending['fillter']  += 1; break;
          case 2: $pending['fillter'] += 1; break;
          case 2: $registered['fillter'] += 1; break;*/
          case 3: $accepted['fillter']    += 1; break;
          case 4: $dispatched['fillter'] += 1;  break;
          case 5: $delivered['fillter']   += 1; break;
          case 6: $cancelled['fillter']   += 1; break;
          case 7: $rejected['fillter']  += 1; break;
          case 8: $uncollectible['fillter'] += 1; break;
          case 12: $unassigned['fillter'] += 1; break;
          default: break;
        }
      }
    }

    $delivery = 0;
    $pickup   = 0;
    $pidafacil   = 0;

    //se suman los tipos de servicio de acuerdo al estado del pedido para poder filtrar
    foreach ($orders as $k => $val){
      $staId=0;
      foreach($val->statusLogs as $d){
        $staId=$d->order_status_id;
      }
      if($staId == 1 || $staId == 2){
        if($res_id == 0){
          if($staId == 1){
            switch ($val->service_type_id){
              case 1:
              $pending['delivery'] += 1;
              break;
              case 2:
              $pending['pickup'] += 1;
              break;
              case 3:
              $pending['pidafacil'] += 1;
              break;
              default:
              break;
            }
          }else{
            switch($val->service_type_id){
              case 1:
              $registered['delivery'] += 1;
              break;
              case 2:
              $registered['pickup'] += 1;
              break;
              case 3:
              $registered['pidafacil'] += 1;
              break;
              default:
              break;
            }
          }
        }else{
          switch($val->service_type_id){
            case 1:
            $pending['delivery'] += 1;
            break;
            case 2:
            $pending['pickup'] += 1;
            break;
            case 3:
            $pending['pidafacil'] += 1;
            break;
            default:
            break;
          }
        }
      }
      elseif($staId == 3){
        switch ($val->service_type_id){
          case 1: $accepted['delivery'] += 1; break;
          case 2: $accepted['pickup']   += 1; break;
          case 3: $accepted['pidafacil']   += 1; break;
          default: break;
        }
      }elseif($staId == 4){
        switch($val->service_type_id){
          case 1: $dispatched['delivery'] += 1; break;
          case 2: $dispatched['pickup']   += 1; break;
          case 3: $dispatched['pidafacil']   += 1; break;
          default: break;
        }
      }
      elseif($staId == 5){
        switch($val->service_type_id){
          case 1: $delivered['delivery'] += 1; break;
          case 2: $delivered['pickup']   += 1; break;
          case 3: $delivered['pidafacil']   += 1; break;
          default: break;
        }
      }
      elseif($staId == 6){
        switch($val->service_type_id){
          case 1: $cancelled['delivery'] += 1; break;
          case 2: $cancelled['pickup']   += 1; break;
          case 3: $cancelled['pidafacil']   += 1; break;
          default: break;
        }
      }
      elseif($staId == 7){
        switch($val->service_type_id){
          case 1: $rejected['delivery'] += 1; break;
          case 2: $rejected['pickup']   += 1; break;
          case 3: $rejected['pidafacil']   += 1; break;
          default: break;
        }
      }
      elseif($staId == 8){
        switch ($val->service_type_id) {
          case 1: $uncollectible['delivery']  += 1; break;
          case 2: $uncollectible['pickup']    += 1; break;
          case 3: $uncollectible['pidafacil']       += 1; break;
          default: break;
        }
      }elseif($staId == 12){
        switch ($val->service_type_id) {
          case 1: $unassigned['delivery']  += 1; break;
          case 2: $unassigned['pickup']    += 1; break;
          case 3: $unassigned['pidafacil']       += 1; break;
          default: break;
        }
      }
      switch($val->service_type_id){
        case 1: $delivery  += 1; break;
        case 2: $pickup    += 1; break;
        case 3: $pidafacil    += 1; break;
        default: break;
      }
    }
    $stats = array(
      'pending'   => $pending,
      'registered'=> $registered,
      'accepted'  => $accepted,
      'dispatched'=> $dispatched,
      'delivered' => $delivered,
      'cancelled' => $cancelled,
      'rejected'  => $rejected,
      'uncollectible' => $uncollectible,
      'unassigned' => $unassigned,
      'delivery'  => $delivery,
      'pickup'  => $pickup,
      'pidafacil' => $pidafacil,
    );
    $stats['pending']['fillter'] = $stats['pending']['delivery'] + $stats['pending']['pickup'] + $stats['pending']['pidafacil'];
    $stats['registered']['fillter'] = $stats['registered']['delivery'] + $stats['registered']['pickup'] + $stats['registered']['pidafacil'];
    $stats['accepted']['fillter'] = $stats['accepted']['delivery'] + $stats['accepted']['pickup'] + $stats['accepted']['pidafacil'];
    $stats['dispatched']['fillter'] = $stats['dispatched']['delivery'] + $stats['dispatched']['pickup'] + $stats['dispatched']['pidafacil'];
    $stats['delivered']['fillter'] = $stats['delivered']['delivery'] + $stats['delivered']['pickup'] + $stats['delivered']['pidafacil'];
    $stats['cancelled']['fillter'] = $stats['cancelled']['delivery'] + $stats['cancelled']['pickup'] + $stats['cancelled']['pidafacil'];
    $stats['rejected']['fillter'] = $stats['rejected']['delivery'] + $stats['rejected']['pickup'] + $stats['rejected']['pidafacil'];
    $stats['uncollectible']['fillter'] = $stats['uncollectible']['delivery'] + $stats['uncollectible']['pickup'] + $stats['uncollectible']['pidafacil'];
    $stats['unassigned']['fillter'] = $stats['unassigned']['delivery'] + $stats['unassigned']['pickup'] + $stats['unassigned']['pidafacil'];
    
    return $stats;
  }

  /**
  * Show the specific order.
  * GET /restaurantorders/order/print/{id}
  *
  * @param  int  $id
  * @return Response
  */
  public function order($id){
    $orders = Order::where('order_id', $id)
    ->with('statusLogs')->with('serviceType')->with('paymentMethod')
    ->with('products')->with('users')->get();

    $condi = DB::table("res_conditions")->get();

    return View::make('web.order_print')
    ->with('orders', $orders)
    ->with('condi', $condi);
  }
  /**
  * Show the form for editing the specified resource.
  * POST /restaurant-orders/forward/{id}
  *
  * @param  int  $id
  * @return Response
  */
  public function forward(){
    if(Request::ajax()){

      $order = Order::findOrFail(Input::get("idA"));
      $result = DB::select('
      SELECT MAX(req_order_status_logs.order_status_id) as ultimo
      FROM req_order_status_logs WHERE order_id = ?', array($order->order_id));
      if(isset($result[0])){
        switch ($result[0]->ultimo){
          case 1:
          $order_stat = new OrderStatusLog;
          $order_stat->order_id = $order->order_id;
          $order_stat->user_id = Auth::id();
          $order_stat->order_status_id = 3;
          $order_stat->save();

          $vres = Order::find(Input::get("idA"));
          $vres->viewed_restaurants = 1;
          $vres->save();

          break;
          case 2:
          $order_stat = new OrderStatusLog;
          $order_stat->order_id = $order->order_id;
          $order_stat->user_id = Auth::id();
          $order_stat->order_status_id = 3;
          $order_stat->save();

          $vres = Order::find(Input::get("idA"));
          $vres->viewed_restaurants = 1;
          $vres->save();
          
          break;
          case 3:
          $order_stat = new OrderStatusLog;
          $order_stat->order_id = $order->order_id;
          $order_stat->user_id = Auth::id();
          $order_stat->order_status_id = 4;
          $order_stat->save();

          $vres = Order::find(Input::get("idA"));
          $vres->viewed_restaurants = 1;
          $vres->save();
          break;
          case 4:
          $order_stat = new OrderStatusLog;
          $order_stat->order_id = $order->order_id;
          $order_stat->user_id = Auth::id();
          $order_stat->order_status_id = 5;
          $order_stat->save();

          $vres = Order::find(Input::get("idA"));
          $vres->viewed_restaurants = 1;
          $vres->save();

          break;
          default:
          $order_stat = null;
          break;
        }
      }

      $restaurants = Restaurant::where('orders_allocator_id',Input::get('id_rest'))->get();

      $orders = Order::where(function($query) use ($restaurants){
        foreach($restaurants as $k=>$v){
          $query->orWhere('restaurant_id',$v->restaurant_id);
        }
      })
      ->whereNotNull('address')   
      ->with('statusLogs')->with('serviceType')->with('paymentMethod')
      ->with('products')->with('users')
      ->orderBy('req_orders.created_at', 'desc')->get();

      $usuarioId = OrderRating::select('user_id')->where('order_id', Input::get("idA"))->get();

      /*Correo electronico... */
      $email = DB::Table('com_users')
      ->select('email', 'name', 'last_name')
      ->where('user_id',$usuarioId[0]->user_id)
      ->get();

      $orderCard  = Order::where('order_id',Input::get("idA"))->get();
      $subtotal   = $orderCard[0]->order_total;
      $delivery   = $orderCard[0]->shipping_charge;
      $card_charge = $orderCard[0]->credit_charge;
      $total = number_format(($subtotal + $delivery + $card_charge),2,".",".");
      $detOrder = OrderDetail::where('order_id',Input::get("idA"))->get();
      if($orderCard[0]->service_type_id !== 2){
        $tipoSer = 'Domicilio';
      }elseif ($orderCard[0]->service_type_id == 2){
        $card_charge = 0.00;
        $total = $subtotal;
        $tipoSer = 'Para llevar';
      }else{

        $tipoSer = '';
      }
      $service_type_id = $orderCard[0]->service_type_id;
      if(isset($email[0])){
        $value = array(
          'user_name'       => $orderCard[0]->customer,
          'order_cod'       => $order->order_cod,
          'status'          => 3,
          'service_type'    => $service_type_id,
          'subtotal'        => $subtotal,
          'shipping_charge' => $delivery,
          'card_charge'     => $card_charge,
          'total'           => $total,
          'productos'       => $detOrder,
          'tipoSer'         => $tipoSer,
        );
        $this->email->cambioEstado($email[0]->email, $email[0]->name." ".$email[0]->last_name, $value);
      }
      $credit_card = substr($orderCard[0]->credit_card, -4);
      $credit_card = "***********".$credit_card;
      $orderUpdate = Order::find(Input::get("idA"));
      $orderUpdate->credit_card = $credit_card;
      $orderUpdate->credit_expmonth = 00;
      $orderUpdate->credit_expyear = 0000;
      $orderUpdate->secure_code = 000;
      $orderUpdate->save();

      $stats = $this->stats($orders,Input::get('id_rest'));

      $vres = Order::find(Input::get("idA"));
      $vres->viewed_restaurants = 1;
      $vres->save();

      /**
      * Enviar push notification a usuario para indicar que su orden
      * ha sido aceptada
      **/

      $app_group_id = '43843606-5adb-4a77-a13c-d785769aedcc';

      // Determine the users you plan to message
      $external_user_ids = array($email[0]->email);

      // MESSAGING ENDPOINT VARIABLES ONLY

      $request_url = 'https://api.appboy.com/messages/send';

      // Establish the contents of your messages array
      $android_noti = array('alert' => 'Tu orden ha sido aceptada y se encuentra en proceso de entrega.',
      'title' => 'PidaFacil');
      $apple_noti = array('alert' => 'Tu orden ha sido aceptada y se encuentra en proceso de entrega.',
      'badge' => 1);
      // Instantiate the messages array
      $messages = array('android_push' => $android_noti,
      'apple_push' => $apple_noti);

      // Organize the data to send to the API as another map
      // comprised of your previously defined variables.
      $postData = array(
        'app_group_id' => $app_group_id,
        'external_user_ids' => $external_user_ids,
        'messages' => $messages,
      );

      // END ENDPOINT-SPECIFIC VARIABLES

      // Create the context for the request
      $context = stream_context_create(array(
        'http' => array(
          'method' => 'POST',
          'header' => "Content-Type: application/json\r\n",
          'content' => json_encode($postData)
        )
      ));

      // Send the request
      $response = file_get_contents($request_url, FALSE, $context);

      return Response::json($stats);
    }
  }
  /**
  * Show the form for editing the specified resource.
  * POST /restaurantorders/forward/{id}
  *
  * @param  int  $id
  * @return Response
  */
  public function backward($id){
    $order = Order::findOrFail($id);
    switch ($order->order_status_id){
      case 1:
      $order->order_status_id = 1;
      break;
      case 2:
      $order->order_status_id = 1;
      break;
      case 3:
      $order->order_status_id = 2;
      break;
      case 4:
      $order->order_status_id = 3;
        $vres = Order::find($id);
        $vres->viewed_restaurants = 1;
        $vres->save();
      break;
      case 5:
      $order->order_status_id = 4;
      break;

      default:
      '';
      break;
    }
    $order->save();
  }

  /**
  * Update the specified resource in storage.
  * POST /restaurantorders/cancel/{id}
  *
  * @param  int  $id
  * @return Response
  */

  public function cancel(){
    $date = new DateTime();
    $fecha = $date->format('Y-m-d');

    if(Request::ajax()){
      //Cancelar la orden
      $order = Order::findOrFail(Input::get("idA"));

      $result = DB::select('
      SELECT MAX(req_order_status_logs.order_status_id) as ultimo
      FROM req_order_status_logs WHERE order_id = ? and created_at LIKE "%'.$fecha.'%"', array($order->order_id));
      
      $motorista_asignado = DB::select('SELECT motorista_id FROM req_order_motorista WHERE order_id = ?',array($order->order_id));

      if($result[0]->ultimo >= 1 ){
        $cancel = Input::get('rejected');

        if($cancel >= 5 && $cancel <= 8){
          $order_stat = new OrderStatusLog;
          $order_stat->order_id = $order->order_id;
          $order_stat->user_id = Auth::id();
          $order_stat->order_status_id = Input::get('rejected');
          $order_stat->comment = Input::get('comment');
          $order_stat->save();
        }elseif($cancel == 2){
          $order_stat = new OrderStatusLog;
          $order_stat->order_id = $order->order_id;
          $order_stat->user_id = Auth::id();
          $order_stat->order_status_id = Input::get('rejected');
          $order_stat->comment = Input::get('comment');
          $order_stat->save();
        }
      }

      if(isset($_POST['motorista_id'])){
        DB::table('req_order_motorista')
        ->insert(
        array('motorista_id'=>$_POST['motorista_id'],'order_id' => $order->order_id)
      );
        $moto = Motorista::find($_POST['motorista_id']);
        $moto->estado = 1;
        $moto->save();
      }else{
        if(isset($cancel) && $cancel > 4){
          if(isset($motorista_asignado) && count($motorista_asignado) > 0 ) {
            $moto = Motorista::find($motorista_asignado[0]->motorista_id);
            $moto->estado = 0;
            $moto->save();
          }

          $std1 = Order::find(Input::get("idA"));
          $std1->viewed_pidafacil = 1;
          $std1->save();

          $std2 = Order::find(Input::get("idA"));
          $std2->viewed_restaurants = 1;
          $std2->save();
        }
      }
    }
    if(Input::get('id_rest')>0){
      $id_res = Input::get('id_rest');
    }else{
      $id_res = $order->restaurant_id;
    }
    $restaurant =  Restaurant::findOrFail($id_res);
    if(Input::get('id_rest') > 0){
      $orders = Order::where('restaurant_id', $restaurant->restaurant_id)
      ->whereNotNull('address')
      ->where('created_at','LIKE','%'.$fecha.'%')
      ->with('statusLogs')->with('serviceType')->with('paymentMethod')
      ->with('products')->with('users')
      ->orderBy('req_orders.created_at', 'desc')->get();
    }else{
      $orders = Order::where('service_type_id', 3)
      ->whereNotNull('address')
      ->where('created_at','LIKE','%'.$fecha.'%')
      ->with('statusLogs')->with('serviceType')->with('paymentMethod')
      ->with('products')->with('users')
      ->orderBy('req_orders.created_at', 'desc')->get();

      $res_delivery_pidafacil = DB::table('res_restaurants')
      ->select('restaurant_id')
      ->where('orders_allocator_id',$restaurant->orders_allocator_id)
      ->get();

      /*$o = Order::find(Input::get("idA"));
      $o->restaurant_id = $res_delivery_pidafacil[0]->restaurant_id;
      $o->save();*/

      $std = Order::find(Input::get("idA"));
      $std->viewed_pidafacil = 1;
      $std->save();
    }

  $usuarioId = OrderRating::select('user_id')->where('order_id', Input::get("idA"))->get();

  /* Correo electronico */
  if(Input::get('rejected') == 3 || Input::get('rejected') == 6 || Input::get('rejected') == 7 || Input::get('rejected') == 8){
    $email = DB::Table('com_users')
    ->select('email', 'name', 'last_name')
    ->where('user_id', $usuarioId[0]->user_id)
    ->get();

    $orderCard = Order::where('order_id', Input::get("idA"))->get();
    $total = $orderCard[0]->order_total;

    $sub_credit_card = substr($orderCard[0]->credit_card, -4);
    $new_credit_card = "***********".$sub_credit_card;
    $orderUpdate = Order::find(Input::get("idA"));
    $orderUpdate->credit_card = $new_credit_card;
    $orderUpdate->credit_expmonth = 00;
    $orderUpdate->credit_expyear = 0000;
    $orderUpdate->secure_code = 000;
    $orderUpdate->save();

    $detOrder = OrderDetail::where('order_id', Input::get("idA"))->get();

    if($orderCard[0]->service_type_id == 1){
      $tipoSer = 'Domicilio';
    }elseif ($orderCard[0]->service_type_id == 2){
      $tipoSer = 'Para llevar';
    }elseif ($orderCard[0]->service_type_id == 2){
      $tipoSer = 'Domicilio';
    }else {
      $tipoSer = '';
    }

    $value = array(
      'user_name' => $orderCard[0]->customer,
      'order_cod' => $order->order_cod,
      'status' => Input::get('rejected'),
      'motivo' => Input::get('comment'),
      'motivoRechazo' => Input::get('motivoRechazo'),
      'total' => $total,
      'productos' => $detOrder,
      'tipoSer' => $tipoSer,
    );

    $this->email->cambioEstado($email[0]->email, $email[0]->name . " " . $email[0]->last_name, $value);

    /**
    * Enviar push notification a usuario para indicar que su orden
    * ha sido aceptada
    **/

    $app_group_id = '43843606-5adb-4a77-a13c-d785769aedcc';

    // Determine the users you plan to message
    $external_user_ids = array($email[0]->email);

    // MESSAGING ENDPOINT VARIABLES ONLY

    $request_url = 'https://api.appboy.com/messages/send';

    if(Input::get('rejected') == 6){
      $message = 'Tu pedido ha sido cancelado debido a '.Input::get('comment');
      
      $std1 = Order::find(Input::get("idA"));
      $std1->viewed_pidafacil = 1;
      $std1->save();

      $std2 = Order::find(Input::get("idA"));
      $std2->viewed_restaurants = 1;
      $std2->save();
    }else{
      $message = 'Tu pedido ha sido rechazado debido a '.Input::get('motivo');

      $std1 = Order::find(Input::get("idA"));
      $std1->viewed_pidafacil = 1;
      $std1->save();

      $std2 = Order::find(Input::get("idA"));
      $std2->viewed_restaurants = 1;
      $std2->save();
    }

    // Establish the contents of your messages array
    $android_noti = array('alert' => $message,
    'title' => 'PidaFacil');
    $apple_noti = array('alert' => $message,
    'badge' => 1);

    // Instantiate the messages array
    $messages = array('android_push' => $android_noti,
    'apple_push' => $apple_noti);

    // Organize the data to send to the API as another map
    // comprised of your previously defined variables.
    $postData = array(
      'app_group_id' => $app_group_id,
      'external_user_ids' => $external_user_ids,
      'messages' => $messages,
    );

    // END ENDPOINT-SPECIFIC VARIABLES

    // Create the context for the request
    $context = stream_context_create(array(
      'http' => array(
        'method' => 'POST',
        'header' => "Content-Type: application/json\r\n",
        'content' => json_encode($postData)
      )
    ));

    // Send the request
    $response = file_get_contents($request_url, FALSE, $context);

  }

  if(Input::get('id_rest') > 0){
    $stats = $this->stats($orders,$restaurant->restaurant_id);
  }else{
    $stats = $this->stats($orders,0);
  }

  /*
  * Borrar/actualizar datos de tarjeta de credito.
  *
  * */

  /*if(Input::get("borrarT"))
  {
    $orderUpdate = Order::find($order->order_id);
    $credit_card = $order->credit_card;
    $orderUpdate->credit_card = $credit_card;
    $orderUpdate->credit_expmonth = 00;
    $orderUpdate->credit_expyear = 0000;
    $orderUpdate->secure_code = 000;
    $orderUpdate->save();
  }
  if($cancel>6)
  {
    $orderUpdate = Order::find($order->order_id);
    $credit_card = $order->credit_card;
    $orderUpdate->credit_card = $credit_card;
    $orderUpdate->credit_expmonth = 00;
    $orderUpdate->credit_expyear = 0000;
    $orderUpdate->secure_code = 000;
    $orderUpdate->save();
  }*/

  return Response::json($stats);
}

  /*Función para aceptar una orden sin motorista asignado, se le pondrá estado 12*/

public function sinMotorista(){
  $date = new DateTime();
  $fecha = $date->format('Y-m-d');

  if(Request::ajax()){
    //Cancelar la orden
    $order = Order::findOrFail(Input::get("idA"));

    $result = DB::select('
    SELECT MAX(req_order_status_logs.order_status_id) as ultimo
    FROM req_order_status_logs WHERE order_id = ? and created_at LIKE "%'.$fecha.'%"', array($order->order_id));
    
    $motorista_asignado = DB::select('SELECT motorista_id FROM req_order_motorista WHERE order_id = ?',array($order->order_id));

    if($result[0]->ultimo >= 1 ){
      $cancel = Input::get('rejected');

      if($cancel >= 5 && $cancel <= 8){
        $order_stat = new OrderStatusLog;
        $order_stat->order_id = $order->order_id;
        $order_stat->user_id = Auth::id();
        $order_stat->order_status_id = Input::get('rejected');
        $order_stat->comment = Input::get('comment');
        $order_stat->save();
      }elseif($cancel == 2){
        $order_stat = new OrderStatusLog;
        $order_stat->order_id = $order->order_id;
        $order_stat->user_id = Auth::id();
        $order_stat->order_status_id = 12;
        $order_stat->comment = Input::get('comment');
        $order_stat->save();
      }
    }

    return Response::json($order->order_id);
  }
}


/**
* Remove the specified resource from storage.
* POST /restaurantorders/baddebt/{id}
*
* @param  int  $id
* @return Response
*/
public function baddebt($id){
  $order = Order::findOrFail($id);
  $order->order_status_id=8;
  $order->save();
}

/**
* Remove the specified resource from storage.
* POST /restaurantorders/baddebt/{id}
*
* @param  int  $id
* @return Response
*/
public function reject($id){
  $order = Order::findOrFail($id);
  $order->order_status_id=7;
  $order->save();
}

/**
* Remove the specified resource from storage.
* POST /restaurantorders/baddebt/{id}
*
* @param  int  $id
* @return Response
*/
public function accept(){
  if(Request::ajax()){
    $order = Order::findOrFail(Input::get("idA"));
    $order->order_status_id = 3;
    $order->save();

    $std2 = Order::find(Input::get("idA"));
    $std2->viewed_restaurants = 1;
    $std2->save();
    return Response::json(Input::get("idA"));
  }
}

public function asignar(){
  $date = new DateTime();
  $fecha = $date->format('Y-m-d');

  if(Request::ajax()){
    $idR = Input::get("id_sucursal");
    $idO = Input::get("id_order");
    DB::update('update req_orders set restaurant_id = '.$idR.', viewed_restaurants = 0 where order_id = '.$idO);

    $restaurant =  Restaurant::findOrFail(Input::get('id_sucursal'));
    $orders = Order::where('restaurant_id', $restaurant->restaurant_id)
    ->whereNotNull('address')
    ->where('created_at','LIKE','%'.$fecha.'%')
    ->with('statusLogs')->with('serviceType')->with('paymentMethod')
    ->with('products')->with('users')
    ->orderBy('req_orders.created_at', 'desc')->get();
    $stats = $this->stats($orders,$restaurant->restaurant_id);

    return Response::json($stats);
  }
}

public function motorista(){
  $mot1 = DB::select("SELECT max(motoristas.motorista_id) as 'motorista_id', concat(motoristas.nombre,' ',motoristas.apellido) as 'nombre_motorista'
  FROM motoristas where motoristas.motorista_id not in (SELECT motorista_id FROM req_order_motorista) group by motoristas.motorista_id");

  $motoristas = DB::select("SELECT motorista_id,concat(nombre,' ',motoristas.apellido) as 'nombre_motorista' FROM motoristas WHERE estado = 0 or estado = 1 order by motorista_id ASC");

  if(isset($mot1[0]) && $mot1[0]->motorista_id != null){
    if(isset($motoristas[0]) ){
      if(count($motoristas) >= count($mot1)){
        for($i = 0; $i < count($motoristas); $i++){
          for($j = 0; $j < count($mot1); $j++){
            if($motoristas[$i]->motorista_id == $mot1[$j]->motorista_id){
              unset($motoristas[$i]);
              $i++;
            }
          }
        }
      }else{
        for($j = 0; $j < count($mot1); $j++){
          for($i = 0; $i < count($motoristas); $i++){
            if($motoristas[$i]->motorista_id == $mot1[$j]->motorista_id){
              unset($mot1[$j]);
              $j++;
            }
          }
        }
      }
      $motoristas = array_merge($motoristas,$mot1);
    }else{
      $motoristas = $mot1;
    }
  }
  return $motoristas;
}

public function deliveryPidafacil(){
  $date = new DateTime();
  $fecha = $date->format('Y-m-d');

  $limit = Order::where('service_type_id',3)->whereNotNull('address')->where('created_at','LIKE','%'.$fecha.'%')->count();
  $or = Order::where('service_type_id',3)->whereNotNull('address')->where('created_at','LIKE','%'.$fecha.'%')->with('motoristas')->get();
  $motoristas = $this->motorista();
  $res = Restaurant::get();
  $pending = array('fillter'=>0,'delivery'=>0,'pickup'=>0);
  $registered = array('fillter'=>0,'delivery'=>0,'pickup'=>0);
  $accepted = array('fillter'=>0,'delivery'=>0,'pickup'=>0);
  $dispatched = array('fillter'=>0,'delivery'=>0,'pickup'=>0);
  $delivered = array('fillter'=>0,'delivery'=>0,'pickup'=>0);
  $cancelled = array('fillter'=>0,'delivery'=>0,'pickup'=>0);
  $rejected = array('fillter'=>0,'delivery'=>0,'pickup'=>0);
  $uncollectible = array('fillter'=>0,'delivery'=>0,'pickup'=>0);
  $unassigned = array('fillter'=>0,'delivery'=>0,'pickup'=>0);
  $orders="";
  $condi="";
  $sections="";

  if($limit > 0){

    $delivery = 0;
    $pickup   = 0;

    if(isset($_GET['busqueda'])){
      $userID=DB::table("com_users")
      ->select('user_id')
      ->orWhere('name','LIKE','%'.Input::get("busqueda").'%')
      ->orWhere('last_name','LIKE','%'.Input::get("busqueda").'%')
      ->orWhere('phone',Input::get("busqueda"))
      ->get();
      $orderRating = 0;
      if(count($userID)>0){
        $orderRating = DB::table("req_order_ratings")
        ->select('order_id')
        ->where(function($query) use ($userID){
          foreach($userID as $idU){
            $query->orWhere('user_id',$idU->user_id);
          }
        })
        ->get();
      }

      $orders = Order::where(function($query) use ($orderRating){
        $query->where('order_cod','LIKE','%'.Input::get("busqueda").'%')
        ->orWhere('address','LIKE','%'.Input::get("busqueda").'%');
        if($orderRating!=0) {
          foreach ($orderRating as $idO) {
            $query->orWhere('order_id', $idO->order_id);
          }
        }
      })
      #->where('service_type_id',3)
      ->where('created_at','LIKE','%'.$fecha.'%')
      #->whereNotNull('address')
      ->with('statusLogs')->with('serviceType')->with('paymentMethod')
      ->with('products')->with('users')
      ->orderBy('req_orders.created_at', 'desc')->get();
    }else{
      $orders = Order::
      #->where('service_type_id',3)
      where('created_at','LIKE','%'.$fecha.'%')
      ->with('statusLogs')->with('serviceType')->with('paymentMethod')
      ->with('products')->with('users')
      ->orderBy('req_orders.created_at', 'desc')->get();
    }
    $condi = DB::table("res_conditions")->get();
    $stats = $this->stats($orders,0);
  }else{
    $orders = null;
    $stats = null;
  }

  //Verifica si el restarante tiene mas de un despachador/orders_allocator_id entre sus sucursales.

  $MasDeUnDespacho = array();
  if($limit > 0){
    foreach ($orders as $k => $v){
      $restaruantsHijos = Restaurant::where('parent_restaurant_id', $v->id)->get();
      $MasDeUnDespacho[$v->id] = true;
      if(isset($restaruantsHijos[0])){
        $alocatorId1 = $restaruantsHijos[0]->orders_allocator_id;
        if(count($restaruantsHijos) > 0){
          foreach ($restaruantsHijos as $key => $value) {
            if ($alocatorId1 != $value->orders_allocator_id) {
              $MasDeUnDespacho[$v->id] = false;
              break;
            }
          }
        }
      }
    }
  }

  $zonas = Zone::select('zone_id', DB::raw('CONCAT(zone, " - $", price) AS name_zona'))
          ->join('pf.com_shipping_prices', 'pf.com_zones.shipping_price_id', '=', 'pf.com_shipping_prices.shipping_price_id')
          ->lists('name_zona', 'zone_id');
        
  $combobox = array('none' => "--Seleccione una zona") + $zonas;
  $selected = array();

  $estados = DB::select('
    select * FROM req_order_status where order_status_id <> 9 and order_status_id <> 11
    ');

  if(empty($motoristas)){
    return View::make('web.delivery_pidafacil', compact('combobox', 'selected'))
    ->with('orders', $orders)
    ->with('stats', $stats)
    ->with('condi',$condi)
    ->with('MasDeUnDespacho',$MasDeUnDespacho)
    ->with('estados',$estados)
    ->with('res', $res);
  }else{
    return View::make('web.delivery_pidafacil', compact('combobox', 'selected'))
    ->with('orders', $orders)
    ->with('stats', $stats)
    ->with('condi',$condi)
    ->with('motoristas',$motoristas)
    ->with('MasDeUnDespacho',$MasDeUnDespacho)
    ->with('estados',$estados)
    ->with('res', $res);
  }
}

public function time(){
  $result = DB::select('
  SELECT os.order_status_id, ro.created_at
  FROM req_order_status_logs as os INNER JOIN req_orders as ro ON os.order_id= ro.order_id WHERE os.order_id = ? group by os.order_status_id DESC limit 1' , array(Input::get("id")));
  return  Response::json($result);
}

//Funcion para el manejo del sonido en las alertas:
public function sond_alert(){
  if(Input::get("sonido")==1)
    $mensaje = 1;
  else
    $mensaje = 0;

  DB::update("UPDATE res_restaurants SET notification_sound = ".$mensaje." WHERE restaurant_id = ".Input::get("id"));

  return Response::json($mensaje);
}
  
  //Agregar observación a una orden
  public function addComment(){
    if(Request::ajax()) {

      $id_orden = Input::get('orden_id');
      $motivo = Input::get('motivo');
      $comentario = Input::get('comment');

      $note = new OrderComment;
      $note->order_id = $id_orden;
      $note->comment = $comentario;
      $note->motivo = $motivo;
      $note->user_id = Auth::user()->user_id;
      $note->user_name = Auth::user()->name;
      $note->user_lastname = Auth::user()->last_name;
      $note->save();
      
      return Response::json(Input::get("orden_id"));
    }
  }

  //obtengo el total de observaciones hechas a una orden en especifico
  public function countObservacion(){
    if(Request::ajax()){

      $id_orden = Input::get('orden_id');
      $total = OrderComment::where('order_id',$id_orden)->count();

      return Response::json($total);
    }
  }

  //obtengo todas las observaciones de una orden en especifico
  public function allObservaciones(){
    if(Request::ajax()){

      $id_orden = Input::get('orden_id');
      $notas = OrderComment::where('order_id',$id_orden)->get();
      $html = '';
      $flag=0;

      foreach ($notas as $row){
        $html.= "<div class='row'>
              <div class='col-md-12' style='font-weight:normal;'>
                <div class='col-md-3' style='border:solid 1px; height:100px;'>
                  <p>".$row['created_at']."</p>
                </div>
                <div class='col-md-3' style='border:solid 1px; height:100px;'>
                  <p>".$row['motivo']."</p>
                </div>
                <div class='col-md-3' style='border:solid 1px; height:100px; overflow-y:auto;'>
                  <p>".$row['comment']."</p>
                </div>
                <div class='col-md-3' style='border:solid 1px; height:100px;'>
                  <p>".$row['user_name'].' '.$row['user_lastname']."</p>
                </div>
              </div>
            </div>";
        $flag++;
      }
      return Response::json($html);
    }
  }

  //funcion para obtener las nuevas ordenes y mostrarlas en visor delivery
  public function new_orders(){
    if(Request::ajax()){
      $date = new DateTime();
      $fecha = $date->format('Y-m-d');
    /*Query para traer las notificaciones*/
      $orden = DB::select('
                SELECT o.order_id , o.order_cod, o.service_type_id,r.name
                FROM req_orders as o 
                inner join res_restaurants as r on r.restaurant_id = o.restaurant_id
                WHERE o.viewed_pidafacil = 0
                and o.created_at LIKE "%'.$fecha.'%"');

      return Response::json($orden);
    }
  }

  //funcion para obtener nuevas ordenes y mostrarlas en visor call center
  public function new_orders_cc(){
    if(Request::ajax()){

      $date = new DateTime();
      $fecha = $date->format('Y-m-d');
      $restaurant = Input::get('rest');

      $orders = DB::select('
                SELECT o.order_id , o.order_cod, o.service_type_id,r.name,o.restaurant_id
                FROM req_orders as o 
                inner join res_restaurants as r on r.restaurant_id = o.restaurant_id 
                WHERE ((o.service_type_id = 3
                and o.viewed_restaurants = 0) or (o.service_type_id <> 3
                and o.viewed_restaurants = 0) or ((o.service_type_id = 2 or o.service_type_id = 1)
                and o.viewed_restaurants = 0))
                and o.created_at LIKE "%'.$fecha.'%"
                GROUP BY o.order_id');
      return Response::json($orders);
    }
  }

  //obtener nuevas ordenes para mostrarlas en visor del restaurante
  public function new_orders_rest(){
    if(Request::ajax()){

      $date = new DateTime();
      $fecha = $date->format('Y-m-d');
      $restaurant = Input::get('rest');

      $orders = DB::select('
                  SELECT o.order_id, r.name, o.restaurant_id, o.order_cod, o.service_type_id,
                  MAX(l.order_status_id) as status
                  FROM req_orders as o
                  INNER JOIN res_restaurants as r on r.restaurant_id = o.restaurant_id 
                  INNER JOIN req_order_status_logs as l ON l.order_id = o.order_id
                  WHERE r.orders_allocator_id = ?
                  and ((o.service_type_id = 3 and viewed_pidafacil = 1
                  and o.viewed_restaurants = 0) or (o.service_type_id <> 3
                  and o.viewed_restaurants = 0) or ((o.service_type_id = 2 or o.service_type_id = 1)
                  and o.viewed_restaurants = 0))
                  and o.created_at LIKE "%'.$fecha.'%"
                  GROUP BY o.order_id,
                  o.order_cod,
                  o.service_type_id', array($restaurant[0]));

      return Response::json($orders);
    }
  }

  public function editOrder($id){
    $response['datos'] = Order::findOrFail($id);
    $response['moto'] = DB::select('
      SELECT mot.id,mt.motorista_id as motoID, ord.order_cod, mt.nombre as name FROM pf.req_order_motorista as mot
      inner join req_orders as ord on mot.order_id = ord.order_id
      inner join motoristas as mt on mot.motorista_id = mt.motorista_id
      where ord.order_id="'.$id.'" 
    ');
    $response['estado'] = OrderStatusLog::where('order_id','=',$id)->orderBy('created_at', 'desc')->first();
    
    if($response['datos']->service_type_id == 2){
      $response['address'] = "Para Llevar";
    }else{
      $response['address'] = Address::findOrFail($response['datos']->address_id);  
    }
    
    if($response['datos']->service_type_id == 2){
      $response['zone'] = "Para Llevar";
    }else{
      $response['zone'] = Zone::findOrFail($response['address']->zone_id);  
    }
    
    Log::info($response['zone']);

    return $response;
  }

  public function actualizar_orden(){
    if(Request::ajax()){

      $ord = Order::findOrFail(Input::get('orden_id'));
      $ord->customer = Input::get('nombre');
      $ord->address = Input::get('direccion');
      $ord->customer_phone = Input::get('telefono');
      $ord->save();

      $direc = DB::table('req_orders')
                ->select('address_id')
                ->where('order_id',Input::get('orden_id'))
                ->get();

      DB::table('diner_addresses')
        ->where('address_id', $direc[0]->address_id)
        ->update(['zone_id' => Input::get('zonaN')]);

      $status = Input::get('estado');
      $moto = Input::get('motorista');
      $userBad = Input::get('marcar');

      if($status != 'null'){
        $est_new = new OrderStatusLog;
        $est_new->order_id = Input::get('orden_id');
        $est_new->order_status_id = $status;
        $est_new->user_id = Auth::user()->user_id;
        $est_new->save();
      }

      if($status == 3 && $ord->service_type_id != 2){
        $user_id = DB::table('com_users')
                  ->join('diner_addresses', 'com_users.user_id', '=', 'diner_addresses.user_id')
                  ->select('diner_addresses.user_id')
                  ->where('diner_addresses.address_id',$direc[0]->address_id)
                  ->get();

        /*Correo electronico... */
        $email = DB::Table('com_users')
          ->select('email', 'name', 'last_name')
          ->where('user_id',$user_id[0]->user_id)
          ->get();

        if(isset($email[0])){
          $value = array(
            'user_name'       => $ord->customer,
            'order_cod'       => $ord->order_cod,
            'status'          => 3,
            'service_type'    => $ord->service_type_id,
            'subtotal'        => $ord->subtotal,
            'shipping_charge' => $ord->shipping_charge,
            'card_charge'     => $ord->credit_charge,
          );
          $this->email->cambioEstado($email[0]->email, $email[0]->name." ".$email[0]->last_name, $value);
        }

        $app_group_id = '43843606-5adb-4a77-a13c-d785769aedcc';

        // Determine the users you plan to message
        $external_user_ids = array($email[0]->email);

        // MESSAGING ENDPOINT VARIABLES ONLY

        $request_url = 'https://api.appboy.com/messages/send';

        // Establish the contents of your messages array
        $android_noti = array('alert' => 'Tu orden ha sido aceptada y se encuentra en proceso de entrega.',
        'title' => 'PidaFacil');
        $apple_noti = array('alert' => 'Tu orden ha sido aceptada y se encuentra en proceso de entrega.',
        'badge' => 1);
        // Instantiate the messages array
        $messages = array('android_push' => $android_noti,
        'apple_push' => $apple_noti);

        // Organize the data to send to the API as another map
        // comprised of your previously defined variables.
        $postData = array(
          'app_group_id' => $app_group_id,
          'external_user_ids' => $external_user_ids,
          'messages' => $messages,
        );

        // END ENDPOINT-SPECIFIC VARIABLES

        // Create the context for the request
        $context = stream_context_create(array(
          'http' => array(
            'method' => 'POST',
            'header' => "Content-Type: application/json\r\n",
            'content' => json_encode($postData)
          )
        ));

        // Send the request
        $response = file_get_contents($request_url, FALSE, $context);
      }

      if($status >= 3){
        $stdE = Order::find(Input::get('orden_id'));
        $stdE->viewed_restaurants = 1;
        $stdE->save();
      }

      if($moto != 'null'){

        DB::table('req_order_motorista')
          ->where('order_id', Input::get('orden_id'))
          ->update(['motorista_id' => $moto]);
      }

      if($userBad != 0){
        $user_id = DB::table('com_users')
                    ->join('diner_addresses', 'com_users.user_id', '=', 'diner_addresses.user_id')
                    ->select('diner_addresses.user_id')
                    ->where('diner_addresses.address_id',$userBad)
                    ->get();

        DB::table('com_users')
          ->where('user_id', $user_id[0]->user_id)
          ->update(['sospecha' => 1]);
      }

      return Response::json(Input::get('orden_id'));
    }  
  }

  public function visorDelivery(){

    $date = new DateTime();
    $fecha = $date->format('Y-m-d');
    $condi="";

    $res = Restaurant::get();
    $orders = Order::where('service_type_id', 3)
              ->where('created_at','LIKE','%'.$fecha.'%')
              ->where('viewed_pidafacil',1)
              ->get();

    $condi = DB::table("res_conditions")->get();
    $stats = $this->stats($orders,0);

    return View::make('web.visor_delivery')
    ->with('orders', $orders)
    ->with('condi', $condi)
    ->with('res', $res);
  }

  public function time_order(){
    $date = new DateTime();
    $fecha = $date->format('Y-m-d');
    
    if(Request::ajax()){
      /*$times = DB::table('req_orders')
            ->select('order_id', 'created_at')
            ->where('service_type_id', 3)
            ->where('created_at','LIKE','%'.$fecha.'%')
            ->where('viewed_pidafacil',1)
            ->get();*/

      $times = DB::select('
                SELECT max(os.order_status_id) as order_status_id, 
                ord.created_at, 
                ord.order_id 
                FROM req_order_status_logs AS os 
                INNER JOIN req_orders AS ord 
                ON os.order_id = ord.order_id 
                WHERE ord.service_type_id = 3 
                AND ord.created_at LIKE "%'.$fecha.'%" 
                AND ord.viewed_pidafacil = 1 
                group by ord.order_id
                ORDER BY os.created_at DESC
              ');
      return Response::json($times);    
    }
  }

  public function ordenes_asignadas(){
    if(Request::ajax()){
      $date = new DateTime();
      $fecha = $date->format('Y-m-d');
    /*Query para traer las notificaciones*/
      $orden = DB::select('
                  SELECT max(os.order_status_id) as order_status_id, o.order_id , o.order_cod, o.service_type_id,r.name
                  FROM req_orders as o 
                  inner join res_restaurants as r on r.restaurant_id = o.restaurant_id
                  inner join req_order_status_logs as os on o.order_id = os.order_id
                  WHERE o.viewed_pidafacil = 1
                  and service_type_id = 3
                  and o.created_at LIKE "%'.$fecha.'%"
                  group by o.order_id
                  ORDER BY os.created_at DESC
                ');

      return Response::json($orden);
    }
  }

  public function reportes(){

    $motoristas = DB::select('select * from motoristas LIMIT 10');
    $res = Restaurant::get();
    $orders = Order::where('service_type_id', 3)
              ->where('viewed_pidafacil',1)
              ->get();

    $condi = DB::table("res_conditions")->get();

    return View::make('web.reportes')
    
    ->with('motoristas', $motoristas);
  }

  /*public function busqueda2(){
    $motivo = $_POST['motivo_busqueda'];
    if($motivo == 'fecha'){
      $fecha1 = $_POST['datepicker1'];
      $fecha2 = $_POST['datepicker2'];

      $ordenes = DB::select('
        select ord.order_id, ord.created_at, ord.payment_method_id, ord.address,res.name,mot.nombre, cz.zone,max(os.order_status_id) as order_status_id
        from req_orders as ord
        inner join res_restaurants as res ON ord.restaurant_id = res.restaurant_id
        inner join req_order_motorista as rm ON ord.order_id = rm.order_id
        inner join motoristas as mot ON rm.motorista_id = mot.motorista_id
        inner join diner_addresses as da ON ord.address_id = da.address_id
        inner join com_zones as cz ON da.zone_id = cz.zone_id
        inner join req_order_status_logs as os on ord.order_id = os.order_id
        where (ord.created_at BETWEEN "'.$fecha1.'" AND "'.$fecha2.'") and ord.service_type_id = 3
        group by ord.order_id
        ');
    }

    return View::make('web.reportes')->with('ordenes', $ordenes);
  }*/

  public function busqueda_ordenes(){

    if(Request::ajax()){
      $motivo = Input::get('motivo');

      if($motivo == 'fecha'){
        $fecha1 = Input::get('fecha_inicio');
        $fecha2 = Input::get('fecha_fin');

        Log::info('Fechas: '.$fecha1. ' '.$fecha2);

        $ordenes = DB::select('
          select ord.order_id, ord.order_cod, ord.customer, ord.shipping_charge, ord.pay_change, ord.created_at, da.reference, ord.order_total, ord.payment_method_id, ord.address, res.name, res.commission_percentage, mot.nombre, cz.zone, cz.cobro_motoboy, if(max(os.order_status_id) > 9,"Pendiente",max(os.order_status_id)) AS order_status_id, max(l.created_at) as tiempoC
          from req_orders as ord
          inner join res_restaurants as res ON ord.restaurant_id = res.restaurant_id
          inner join mensajero as rm ON ord.order_id = rm.order_id
          inner join motoristas as mot ON rm.motorista_id = mot.motorista_id
          inner join diner_addresses as da ON ord.address_id = da.address_id
          inner join com_zones as cz ON da.zone_id = cz.zone_id
          inner join req_order_status_logs l 
            ON l.order_id = ord.order_id 
          inner join req_order_status os 
            ON os.order_status_id = l.order_status_id 
          inner join (SELECT order_id, 
            Max(order_status_id) AS order_status_id 
              FROM   pf.req_order_status_logs 
              GROUP  BY order_id) AS lot 
              ON lot.order_id = ord.order_id 
              AND lot.order_status_id = os.order_status_id 
          where (ord.created_at BETWEEN "'.$fecha1.'" AND "'.$fecha2.'") 
          and ord.service_type_id = 3
          group by ord.order_id
        ');
      }elseif($motivo == 'moto'){
        $moto = Input::get('motorista');
        $date = new DateTime();
        $fecha = $date->format('Y-m-d');

        if($moto == 'todos'){
          $ordenes = DB::select('
            select ord.order_id, ord.order_cod, ord.customer, ord.shipping_charge, ord.pay_change, ord.created_at, da.reference, ord.order_total, ord.payment_method_id, ord.address, res.name, res.commission_percentage, mot.nombre, cz.zone, cz.cobro_motoboy, if(max(os.order_status_id) > 9,"Pendiente",max(os.order_status_id)) AS order_status_id, max(l.created_at) as tiempoC
            from req_orders as ord
            inner join res_restaurants as res ON ord.restaurant_id = res.restaurant_id
            inner join mensajero as rm ON ord.order_id = rm.order_id
            inner join motoristas as mot ON rm.motorista_id = mot.motorista_id
            inner join diner_addresses as da ON ord.address_id = da.address_id
            inner join com_zones as cz ON da.zone_id = cz.zone_id
            inner join req_order_status_logs l 
              ON l.order_id = ord.order_id 
            inner join req_order_status os 
              ON os.order_status_id = l.order_status_id 
            inner join (SELECT order_id, 
              Max(order_status_id) AS order_status_id 
                FROM   pf.req_order_status_logs 
                GROUP  BY order_id) AS lot 
                ON lot.order_id = ord.order_id 
                AND lot.order_status_id = os.order_status_id 
            where ord.created_at LIKE "%'.$fecha.'%" and ord.service_type_id = 3
            group by ord.order_id
          ');          
        }else{
          $ordenes = DB::select('
            select ord.order_id, ord.order_cod, ord.customer, ord.shipping_charge, ord.pay_change, da.reference, res.commission_percentage, ord.order_total, ord.created_at, ord.payment_method_id, ord.address,res.name,mot.nombre, cz.zone, cz.cobro_motoboy, if(max(os.order_status_id) > 9,"Pendiente",max(os.order_status_id)) AS order_status_id, max(l.created_at) as tiempoC
            from req_orders as ord
            inner join res_restaurants as res ON ord.restaurant_id = res.restaurant_id
            inner join mensajero as rm ON ord.order_id = rm.order_id
            inner join motoristas as mot ON rm.motorista_id = mot.motorista_id
            inner join diner_addresses as da ON ord.address_id = da.address_id
            inner join com_zones as cz ON da.zone_id = cz.zone_id
            inner join req_order_status_logs l 
              ON l.order_id = ord.order_id 
            inner join req_order_status os 
              ON os.order_status_id = l.order_status_id 
            inner join (SELECT order_id, 
              Max(order_status_id) AS order_status_id 
                FROM   pf.req_order_status_logs 
                GROUP  BY order_id) AS lot 
                ON lot.order_id = ord.order_id 
                AND lot.order_status_id = os.order_status_id
            where rm.motorista_id = '.$moto.' and ord.created_at LIKE "%'.$fecha.'%" and ord.service_type_id = 3
            group by ord.order_id
          ');
        }

      }elseif($motivo == 'pago'){
        $tipo_pago = Input::get('pago');
        $date = new DateTime();
        $fecha = $date->format('Y-m-d');

        if($tipo_pago == 'todos'){
          $ordenes = DB::select('
            select ord.order_id, ord.order_cod, ord.customer, ord.shipping_charge, ord.pay_change, da.reference, res.commission_percentage, ord.order_total, ord.created_at, ord.payment_method_id, ord.address,res.name,mot.nombre, cz.zone, cz.cobro_motoboy, if(max(os.order_status_id) > 9,"Pendiente",max(os.order_status_id)) AS order_status_id, max(l.created_at) as tiempoC
            from req_orders as ord
            inner join res_restaurants as res ON ord.restaurant_id = res.restaurant_id
            inner join mensajero as rm ON ord.order_id = rm.order_id
            inner join motoristas as mot ON rm.motorista_id = mot.motorista_id
            inner join diner_addresses as da ON ord.address_id = da.address_id
            inner join com_zones as cz ON da.zone_id = cz.zone_id
            inner join req_order_status_logs l 
              ON l.order_id = ord.order_id 
            inner join req_order_status os 
              ON os.order_status_id = l.order_status_id 
            inner join (SELECT order_id, 
                Max(order_status_id) AS order_status_id 
              FROM   pf.req_order_status_logs 
              GROUP  BY order_id) AS lot 
              ON lot.order_id = ord.order_id 
              AND lot.order_status_id = os.order_status_id
            where ord.created_at LIKE "%'.$fecha.'%" and ord.service_type_id = 3
            group by ord.order_id
          ');
        }else{
          $ordenes = DB::select('
            select ord.order_id, ord.order_cod, ord.customer, ord.shipping_charge, ord.pay_change, da.reference, res.commission_percentage, ord.order_total, ord.created_at, ord.payment_method_id, ord.address,res.name,mot.nombre, cz.zone, cz.cobro_motoboy, if(max(os.order_status_id) > 9,"Pendiente",max(os.order_status_id)) AS order_status_id, max(l.created_at) as tiempoC
            from req_orders as ord
            inner join res_restaurants as res ON ord.restaurant_id = res.restaurant_id
            inner join mensajero as rm ON ord.order_id = rm.order_id
            inner join motoristas as mot ON rm.motorista_id = mot.motorista_id
            inner join diner_addresses as da ON ord.address_id = da.address_id
            inner join com_zones as cz ON da.zone_id = cz.zone_id
            inner join req_order_status_logs l 
              ON l.order_id = ord.order_id 
            inner join req_order_status os 
              ON os.order_status_id = l.order_status_id 
            inner join (SELECT order_id, 
              Max(order_status_id) AS order_status_id 
                FROM   pf.req_order_status_logs 
                GROUP  BY order_id) AS lot 
                ON lot.order_id = ord.order_id 
                AND lot.order_status_id = os.order_status_id
            where ord.payment_method_id = '.$tipo_pago.' and ord.created_at LIKE "%'.$fecha.'%" and ord.service_type_id = 3
            group by ord.order_id
          ');
        }
      }
      #echo "<pre>"; print_r($ordenes); echo "</pre>"; die();
      Log::info($ordenes);
      return Response::json($ordenes);
    }
  }
}
