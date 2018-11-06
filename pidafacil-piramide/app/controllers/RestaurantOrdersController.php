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
  public function show($id)
  {
    /*
    $limit = User::find(1)->restaurants->count();
    if($limit > 0){
    $resu = User::find(1)->restaurants;
    return Response::json($resu, 200);
  }else{
  return $limit;
}
*/
$restaurant =  Restaurant::findOrFail($id);
$limit = Order::where('restaurant_id', $restaurant->restaurant_id)
->whereNotNull('address')->count();

$pending = array('fillter'=>0,'delivery'=>0,'pickup'=>0);
$registered = array('fillter'=>0,'delivery'=>0,'pickup'=>0);
$accepted = array('fillter'=>0,'delivery'=>0,'pickup'=>0);
$dispatched = array('fillter'=>0,'delivery'=>0,'pickup'=>0);
$delivered = array('fillter'=>0,'delivery'=>0,'pickup'=>0);
$cancelled = array('fillter'=>0,'delivery'=>0,'pickup'=>0);
$rejected = array('fillter'=>0,'delivery'=>0,'pickup'=>0);
$uncollectible = array('fillter'=>0,'delivery'=>0,'pickup'=>0);
$orders="";
$condi="";
if($limit > 0) {


  $delivery = 0;
  $pickup   = 0;


  if(isset($_GET['busqueda']))
  {
    $userID=DB::table("com_users")
    ->select('user_id')
    ->orWhere('name','LIKE','%'.Input::get("busqueda").'%')
    ->orWhere('last_name','LIKE','%'.Input::get("busqueda").'%')
    ->orWhere('phone',Input::get("busqueda"))
    ->get();
    $orderRating = 0;
    if(count($userID)>0)
    {
      $orderRating = DB::table("req_order_ratings")
      ->select('order_id')
      ->where(function($query) use ($userID){
        foreach($userID as $idU)
        {
          $query->orWhere('user_id',$idU->user_id);
        }
      })
      ->get();
    }

    $orders = Order::where('restaurant_id', $restaurant->restaurant_id)
    ->where(function($query) use ($orderRating) {
      $query->where('order_cod','LIKE','%'.Input::get("busqueda").'%')
      ->orWhere('address','LIKE','%'.Input::get("busqueda").'%');
      if($orderRating!=0) {
        foreach ($orderRating as $idO) {
          $query->orWhere('order_id', $idO->order_id);
        }
      }
    })
    ->whereNotNull('address')
    ->with('statusLogs')->with('serviceType')->with('paymentMethod')
    ->with('products')->with('users')
    ->orderBy('req_orders.created_at', 'asc')->get();
  }else
  {
    $orders = Order::where('restaurant_id', $restaurant->restaurant_id)
    ->whereNotNull('address')
    ->with('statusLogs')->with('serviceType')->with('paymentMethod')
    ->with('products')->with('users')
    ->orderBy('req_orders.created_at', 'asc')->get();
  }
  if(Restaurant::where('parent_restaurant_id', $restaurant->restaurant_id)->get())
  {
    $sucursales = Restaurant::where('parent_restaurant_id', $restaurant->restaurant_id)->get();
  }
  $condi = DB::table("res_conditions")->get();
  $stats = $this->stats($orders,$restaurant->restaurant_id);

}else{
  $orders = null;
  $stats = null;
}

if(empty($sucursales))
{
  return View::make('web.visor')
  ->with('orders', $orders)
  ->with('stats', $stats)
  ->with('condi',$condi);
}else{
  return View::make('web.visor')
  ->with('orders', $orders)
  ->with('stats', $stats)
  ->with('condi',$condi)
  ->with('sucursales',$sucursales);
}
}

public function stats($orders, $res_id)
{
  $pending = array('fillter'=>0,'delivery'=>0,'pickup'=>0,'pidafacil'=>0);
  $registered = array('fillter'=>0,'delivery'=>0,'pickup'=>0,'pidafacil'=>0);
  $accepted = array('fillter'=>0,'delivery'=>0,'pickup'=>0,'pidafacil'=>0);
  $dispatched = array('fillter'=>0,'delivery'=>0,'pickup'=>0,'pidafacil'=>0);
  $delivered = array('fillter'=>0,'delivery'=>0,'pickup'=>0,'pidafacil'=>0);
  $cancelled = array('fillter'=>0,'delivery'=>0,'pickup'=>0,'pidafacil'=>0);
  $rejected = array('fillter'=>0,'delivery'=>0,'pickup'=>0,'pidafacil'=>0);
  $uncollectible = array('fillter'=>0,'delivery'=>0,'pickup'=>0,'pidafacil'=>0);
  if($res_id > 0)
  {

    $results = DB::select('
    SELECT MAX(req_order_status_logs.order_status_id) as ultimo, req_orders.order_id
    FROM req_order_status_logs
    LEFT JOIN req_orders ON req_order_status_logs.order_id = req_orders.order_id
    WHERE req_orders.restaurant_id = ? AND NOT ISNULL(req_orders.address)
    GROUP BY req_orders.order_id', array($res_id));
  }else {
    $results = DB::select('
    SELECT MAX(req_order_status_logs.order_status_id) as ultimo, req_orders.order_id
    FROM req_order_status_logs
    LEFT JOIN req_orders ON req_order_status_logs.order_id = req_orders.order_id
    WHERE  req_orders.service_type_id = 3 and req_orders.payment_method_id = 2 AND NOT ISNULL(req_orders.address)
    GROUP BY req_orders.order_id');
  }

  foreach ($results as $value) {
    switch($value->ultimo){
      case 1: $pending['fillter']  += 1; break;
      case 2: $registered['fillter'] += 1; break;
      case 3: $accepted['fillter']  	+= 1; break;
      case 4: $dispatched['fillter'] += 1; break;
      case 5: $delivered['fillter'] 	+= 1; break;
      case 6: $cancelled['fillter'] 	+= 1; break;
      case 7: $rejected['fillter'] 	+= 1; break;
      case 8: $uncollectible['fillter'] += 1; break;
      default: break;
    }
  }
  $delivery = 0;
  $pickup   = 0;
  $pidafacil   = 0;


  //se suman los tipos de servicio de acuerdo al estado del pedido para poder filtrar
  foreach ($orders as $k => $val) {
    $staId=0;
    foreach($val->statusLogs as $d)
    {
      $staId=$d->order_status_id;
    }
    if($staId == 1)
    {
      switch ($val->service_type_id) {
        case 1: $pending['delivery']    += 1; break;
        case 2: $pending['pickup']      += 1; break;
        case 3: $pending['pidafacil']   += 1; break;
        default: break;
      }
    }
    elseif($staId == 3)
    {
      switch ($val->service_type_id) {
        case 1: $accepted['delivery'] += 1; break;
        case 2: $accepted['pickup']   += 1; break;
        case 3: $pending['pidafacil']   += 1; break;
        default: break;
      }
    }
    elseif($staId == 5)
    {
      switch ($val->service_type_id) {
        case 1: $delivered['delivery'] += 1; break;
        case 2: $delivered['pickup']   += 1; break;
        case 3: $pending['pidafacil']   += 1; break;
        default: break;
      }
    }
    elseif($staId == 6)
    {
      switch ($val->service_type_id) {
        case 1: $cancelled['delivery'] += 1; break;
        case 2: $cancelled['pickup']   += 1; break;
        case 3: $pending['pidafacil']   += 1; break;
        default: break;
      }
    }
    elseif($staId == 7)
    {
      switch ($val->service_type_id) {
        case 1: $rejected['delivery'] += 1; break;
        case 2: $rejected['pickup']   += 1; break;
        case 3: $pending['pidafacil']   += 1; break;
        default: break;
      }
    }
    elseif($staId == 8)
    {
      switch ($val->service_type_id) {
        case 1: $uncollectible['delivery']  += 1; break;
        case 2: $uncollectible['pickup']    += 1; break;
        case 3: $pending['pidafacil']       += 1; break;
        default: break;
      }
    }
    switch ($val->service_type_id) {
      case 1: $delivery  += 1; break;
      case 2: $pickup    += 1; break;
      case 3: $pidafacil    += 1; break;
      default: break;
    }
  }
  $stats = array(
    'pending' 	=> $pending,
    'registered'=> $registered,
    'accepted' 	=> $accepted,
    'dispatched'=> $dispatched,
    'delivered' => $delivered,
    'cancelled' => $cancelled,
    'rejected' 	=> $rejected,
    'uncollectible' => $uncollectible,
    'delivery' 	=> $delivery,
    'pickup' 	=> $pickup,
    'pidafacil' => $pidafacil,
  );
  return $stats;
}

/**
* Show the specific order.
* GET /restaurantorders/order/{id}
*
* @param  int  $id
* @return Response
*/
public function order($id)
{
  $n=0;
  $req_products = DB::select('select orders.order_id, details.order_det_id,orders.restaurant_id, product.product_id, product.product
  from req_orders as orders
  inner join req_orders_det as details on orders.order_id = details.order_id
  inner join res_products as product on details.product_id = product.product_id
  where orders.order_id = ?', array( $id));

  foreach ($req_products as $product) {

    $data['producto['.$n.']']=$product;

    $ingredient = DB::select('select resingredient.ingredient_id, resingredient.ingredient,reqingredient.remove from req_product_ingredients reqingredient
    inner join res_ingredients resingredient on reqingredient.ingredient_id =  resingredient.ingredient_id
    where reqingredient.order_det_id = ? and reqingredient.remove != 0',array($product->order_det_id));
    $data['ingredients['.$n.']'] = $ingredient;


    $optioncondition = DB::select('select  orderdetail.order_det_id,conditions.condition,conditionoption.condition_option    from req_orders_det orderdetail
    inner join req_product_conditions_options prodcondition on orderdetail.order_det_id = prodcondition.order_det_id
    inner join res_conditions conditions on conditions.condition_id  =  prodcondition.condition_id
    inner join res_product_conditions_options conditionoption on conditionoption.condition_option_id = prodcondition.condition_option_id
    where orderdetail.order_det_id = ?',array($product->order_det_id));
    $data['optioncondition['.$n.']'] = $optioncondition;


    $n++;

  }
  $data['info'] = DB::select('select orders.restaurant_id,user.user_id, user.email, user.name, user.last_name,orders.order_id, orders.order_total, orders.address_id, address.address_1,
  address.address_2, address.address_name, address.city, address.country_id, address.state,
  orders.order_status_id, order_status.order_status, orders.service_type_id, service.service_type,
  orders.payment_method_id, payment.payment_method, orders.created_at, orders.updated_at from req_orders as orders
  inner join diner_addresses as address on orders.address_id = address.address_id
  inner join req_order_status as order_status on orders.order_status_id = order_status.order_status_id
  inner join res_service_types as service on orders.service_type_id = service.service_type_id
  inner join res_payment_methods as payment on orders.payment_method_id =  payment.payment_method_id
  inner join com_users as user on address.user_id = user.user_id
  where  orders.order_id = ? ', array($id));
  $data['nprods'] = $n;
  return $data;
  //return $results;
}
/**
* Show the form for editing the specified resource.
* POST /restaurant-orders/forward/{id}
*
* @param  int  $id
* @return Response
*/
public function forward()
{
  if(Request::ajax()) {
    $order = Order::findOrFail(Input::get("idA"));
    $result = DB::select('
    SELECT MAX(req_order_status_logs.order_status_id) as ultimo
    FROM req_order_status_logs WHERE order_id = ?', array($order->order_id));
    switch ($result[0]->ultimo) {
      case 1:
      $order_stat = new OrderStatusLog;
      $order_stat->order_id = $order->order_id;
      $order_stat->user_id = Auth::id();
      $order_stat->order_status_id = 3;
      $order_stat->save();
      break;
      case 2:
      $order_stat = new OrderStatusLog;
      $order_stat->order_id = $order->order_id;
      $order_stat->user_id = Auth::id();
      $order_stat->order_status_id = 3;
      $order_stat->save();
      break;
      case 3:
      $order_stat = new OrderStatusLog;
      $order_stat->order_id = $order->order_id;
      $order_stat->user_id = Auth::id();
      $order_stat->order_status_id = 4;
      $order_stat->save();
      break;
      case 4:
      $order_stat = new OrderStatusLog;
      $order_stat->order_id = $order->order_id;
      $order_stat->user_id = Auth::id();
      $order_stat->order_status_id = 5;
      $order_stat->save();
      break;
      default: $order_stat=null; break;
    }


    $restaurant =  Restaurant::findOrFail(Input::get('id_rest'));
    $orders = Order::where('restaurant_id', $restaurant->restaurant_id)
    ->whereNotNull('address')
    ->with('statusLogs')->with('serviceType')->with('paymentMethod')
    ->with('products')->with('users')
    ->orderBy('req_orders.created_at', 'asc')->get();
    foreach($orders as $k => $valuestatus)
    {
      foreach($valuestatus->statusLogs as $k2 => $vals)
      {
        if($vals->order_id == Input::get("idA"))
        {
          $idUser = $vals->user_id;
        }
      }
    }
    $email = DB::Table('com_users')
    ->select('email', 'name', 'last_name')
    ->where('user_id',$idUser)
    ->get();

    $value = array(
      'user_name' => $email[0]->name." ".$email[0]->last_name,
      'order_cod' => $order->order_cod,
      'status'    => 3,
    );
    $this->email->cambioEstado($email[0]->email, $email[0]->name." ".$email[0]->last_name, $value);

    $stats = $this->stats($orders,$restaurant->restaurant_id);

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
public function backward($id)
{
  $order = Order::findOrFail($id);
  switch ($order->order_status_id) {
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
public function cancel()
{
  if(Request::ajax()) {
    // Cancelar la orden
    $order = Order::findOrFail(Input::get("idA"));
    $result = DB::select('
    SELECT MAX(req_order_status_logs.order_status_id) as ultimo
    FROM req_order_status_logs WHERE order_id = ?', array($order->order_id));
    if ($result[0]->ultimo == 1 || $result[0]->ultimo == 2) {
      $cancel = Input::get('rejected');
      if ($cancel >= 6 && $cancel <= 8) {
        $order_stat = new OrderStatusLog;
        $order_stat->order_id = $order->order_id;
        $order_stat->user_id = Auth::id();
        $order_stat->order_status_id = Input::get('rejected');
        $order_stat->comment = Input::get('comment');
        $order_stat->save();
      }
    }
  }
  $restaurant =  Restaurant::findOrFail(Input::get('id_rest'));
  $orders = Order::where('restaurant_id', $restaurant->restaurant_id)
  ->whereNotNull('address')
  ->with('statusLogs')->with('serviceType')->with('paymentMethod')
  ->with('products')->with('users')
  ->orderBy('req_orders.created_at', 'asc')->get();

  foreach($orders as $k => $valuestatus)
  {
    foreach($valuestatus->statusLogs as $k2 => $vals)
    {
      if($vals->order_id == Input::get("idA"))
      {
        $idUser = $vals->user_id;
      }
    }
  }
  $email = DB::Table('com_users')
  ->select('email', 'name', 'last_name')
  ->where('user_id',$idUser)
  ->get();

  $value = array(
    'user_name'     => $email[0]->name." ".$email[0]->last_name,
    'order_cod'     => $order->order_cod,
    'status'        => Input::get('rejected'),
    'motivo'        => Input::get('comment'),
    'motivoRechazo' => Input::get('motivoRechazo'),
  );
  $this->email->cambioEstado($email[0]->email, $email[0]->name." ".$email[0]->last_name, $value);

  $stats = $this->stats($orders,$restaurant->restaurant_id);

  return Response::json($stats);
}

/**
* Remove the specified resource from storage.
* POST /restaurantorders/baddebt/{id}
*
* @param  int  $id
* @return Response
*/
public function baddebt($id)
{
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
public function reject($id)
{
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
public function accept()
{
  if(Request::ajax()) {
    $order = Order::findOrFail(Input::get("idA"));
    $order->order_status_id = 3;
    $order->save();
    return Response::json(Input::get("idA"));
  }
}

public function asignar()
{
  if(Request::ajax()){
    $idR=Input::get("id_sucursal");
    $idO = Input::get("id_order");
    DB::update('update req_orders set restaurant_id = '.$idR.' where order_id = '.$idO);

    $restaurant =  Restaurant::findOrFail(Input::get('id_rest'));
    $orders = Order::where('restaurant_id', $restaurant->restaurant_id)
    ->whereNotNull('address')
    ->with('statusLogs')->with('serviceType')->with('paymentMethod')
    ->with('products')->with('users')
    ->orderBy('req_orders.created_at', 'asc')->get();


    $stats = $this->stats($orders,$restaurant->restaurant_id);

    return Response::json($stats);

  }
}

public function deliveryPidafacil()
{
  $limit = Order::where('service_type_id',3)->whereNotNull('address')->count();
  $or = Order::where('service_type_id',3)->whereNotNull('address')->with('motoristas')->get();
  $res = Restaurant::get();
  $pending = array('fillter'=>0,'delivery'=>0,'pickup'=>0);
  $registered = array('fillter'=>0,'delivery'=>0,'pickup'=>0);
  $accepted = array('fillter'=>0,'delivery'=>0,'pickup'=>0);
  $dispatched = array('fillter'=>0,'delivery'=>0,'pickup'=>0);
  $delivered = array('fillter'=>0,'delivery'=>0,'pickup'=>0);
  $cancelled = array('fillter'=>0,'delivery'=>0,'pickup'=>0);
  $rejected = array('fillter'=>0,'delivery'=>0,'pickup'=>0);
  $uncollectible = array('fillter'=>0,'delivery'=>0,'pickup'=>0);
  $orders="";
  $condi="";

  if($limit > 0) {

    $delivery = 0;
    $pickup   = 0;


    if(isset($_GET['busqueda']))
    {
      $userID=DB::table("com_users")
      ->select('user_id')
      ->orWhere('name','LIKE','%'.Input::get("busqueda").'%')
      ->orWhere('last_name','LIKE','%'.Input::get("busqueda").'%')
      ->orWhere('phone',Input::get("busqueda"))
      ->get();
      $orderRating = 0;
      if(count($userID)>0)
      {
        $orderRating = DB::table("req_order_ratings")
        ->select('order_id')
        ->where(function($query) use ($userID){
          foreach($userID as $idU)
          {
            $query->orWhere('user_id',$idU->user_id);
          }
        })
        ->get();
      }

      $orders = Order::where(function($query) use ($orderRating) {
        $query->where('order_cod','LIKE','%'.Input::get("busqueda").'%')
        ->orWhere('address','LIKE','%'.Input::get("busqueda").'%');
        if($orderRating!=0) {
          foreach ($orderRating as $idO) {
            $query->orWhere('order_id', $idO->order_id);
          }
        }
      })
      ->where('service_type_id',3)
      ->whereNotNull('address')
      ->with('statusLogs')->with('serviceType')->with('paymentMethod')
      ->with('products')->with('users')
      ->orderBy('req_orders.created_at', 'asc')->get();
    }else
    {
      $orders = Order::whereNotNull('address')
      ->where('service_type_id',3)
      ->with('statusLogs')->with('serviceType')->with('paymentMethod')
      ->with('products')->with('users')
      ->orderBy('req_orders.created_at', 'asc')->get();
    }
    $condi = DB::table("res_conditions")->get();
    $stats = $this->stats($orders,0);

  }else{
    $orders = null;
    $stats = null;
  }

  if(empty($sucursales))
  {
    return View::make('web.delivery_pidafacil')
    ->with('orders', $orders)
    ->with('stats', $stats)
    ->with('condi',$condi)
    ->with('res', $res);
  }else{
    return View::make('web.delivery_pidafacil')
    ->with('orders', $orders)
    ->with('stats', $stats)
    ->with('condi',$condi)
    ->with('sucursales',$sucursales)
    ->with('res', $res);
  }
}


}
