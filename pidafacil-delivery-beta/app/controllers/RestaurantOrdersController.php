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
        $restaurant = Restaurant::select('restaurant_id')->where('orders_allocator_id', $id)->get();
        $restaruantsHijos = Restaurant::where('parent_restaurant_id', $id)->get();

        $limit = 0;
        $ids = array();

        for ($i = 0; $i < count($restaurant); $i++) {
            $ids[$i] = $restaurant[$i]->restaurant_id;
        }
        if (count($ids) > 0) {
            $limit = Order::where(function ($query) use ($ids, $id) {
                $query->where(function ($query) use ($ids) {
                    for ($i = 0; $i < count($ids); $i++) {
                        $query->orWhere('restaurant_id', $ids[$i]);
                    }
                    $query
                        ->where(function ($query) {
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
            $restaurantes[$r->restaurant_id] = array('name' => $r->name, "parent" => $r->parent_restaurant_id);
        }

        $pending = array('fillter' => 0, 'delivery' => 0, 'pickup' => 0);
        $registered = array('fillter' => 0, 'delivery' => 0, 'pickup' => 0);
        $accepted = array('fillter' => 0, 'delivery' => 0, 'pickup' => 0);
        $dispatched = array('fillter' => 0, 'delivery' => 0, 'pickup' => 0);
        $delivered = array('fillter' => 0, 'delivery' => 0, 'pickup' => 0);
        $cancelled = array('fillter' => 0, 'delivery' => 0, 'pickup' => 0);
        $rejected = array('fillter' => 0, 'delivery' => 0, 'pickup' => 0);
        $uncollectible = array('fillter' => 0, 'delivery' => 0, 'pickup' => 0);
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
                        })->orWhere(function ($query) use ($id) {
                            $query->where('service_type_id', 2)
                                ->where('restaurant_id', $id);
                        });
                    })
                        ->whereNotNull('address')
                        ->with('statusLogs')->with('serviceType')->with('paymentMethod')
                        ->with('products')->with('users')
                        ->orderBy('req_orders.created_at', 'asc')->get();
                } else {
                    $orders = Order::where(function ($query) use ($ids, $id) {
                    })->orWhere(function ($query) use ($id) {
                        $query->where('service_type_id', 2)
                            ->where('restaurant_id', $id);
                    })
                        ->whereNotNull('address')
                        ->with('statusLogs')->with('serviceType')->with('paymentMethod')
                        ->with('products')->with('users')
                        ->orderBy('req_orders.created_at', 'asc')->get();
                }
            } else {
                if (count($ids) > 0) {
                    $orders = Order::where(function ($query) use ($ids, $id) {
                        $query->where(function ($query) use ($ids, $id) {
                            for ($i = 0; $i < count($ids); $i++) {
                                $query->orWhere('restaurant_id', $ids[$i]);
                            }
                        })->where(function ($query) {
                            $query->orWhere('service_type_id', 1)
                                ->orWhere('service_type_id', 3);
                        })->orWhere(function ($query) use ($id) {
                            $query->where('service_type_id', 2)
                                ->where('restaurant_id', $id);
                        });
                    })
                        ->whereNotNull('address')
                        ->with('statusLogs')->with('serviceType')->with('paymentMethod')
                        ->with('products')->with('users')
                        ->orderBy('req_orders.created_at', 'asc')->get();
                } else {
                    $orders = Order::where(function ($query) use ($ids, $id) {
                    })->orWhere(function ($query) use ($id) {
                        $query->where('service_type_id', 2)
                            ->where('restaurant_id', $id);
                    })
                        ->whereNotNull('address')
                        ->with('statusLogs')->with('serviceType')->with('paymentMethod')
                        ->with('products')->with('users')
                        ->orderBy('req_orders.created_at', 'asc')->get();
                }

            }
            if (Restaurant::where('parent_restaurant_id', $id)->get()) {
                $sucursales = Restaurant::where('parent_restaurant_id', $id)->get();
            }
            $condi = DB::table("res_conditions")->get();
            $stats = $this->stats($orders, $ids);

        } else {
            $orders = null;
            $stats = null;
        }

        //Verifica si el restaurante tiene mas de un despachador/orders_allocator_id entre sus sucursales.
        $MasDeUnDespacho = false;
        if(isset($restaruantsHijos[0])) {
            $alocatorId1 = $restaruantsHijos[0]->orders_allocator_id;

            if (count($restaruantsHijos) > 0) {
                foreach ($restaruantsHijos as $key => $value) {
                    if ($alocatorId1 != $value->orders_allocator_id) {
                        $MasDeUnDespacho = true;
                        break;
                    }
                }
            }
        }

        /**/

        if(empty($sucursales))
        {
            return View::make('web.visor')
                ->with('orders', $orders)
                ->with('stats', $stats)
                ->with('condi',$condi)
                ->with('restaurantes', $restaurantes)
                ->with('MasDeUnDespacho',$MasDeUnDespacho)
                ->with('id',$id);
        }else{
            return View::make('web.visor')
                ->with('orders', $orders)
                ->with('stats', $stats)
                ->with('condi',$condi)
                ->with('sucursales',$sucursales)
                ->with('restaurantes', $restaurantes)
                ->with('MasDeUnDespacho',$MasDeUnDespacho)
                ->with('id',$id);
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
        /*if(count($res_id) > 0)
        {
            $results = DB::table('req_order_status_logs')
                ->join('req_orders','req_order_status_logs.order_id', '=', 'req_orders.order_id')
                ->select('req_order_status_logs.order_status_id as ultimo', 'req_orders.service_type_id as type_id')
                ->where(function($query) use ($res_id){
                    for($i=0; $i < count($res_id); $i++)
                    {
                        $query->orWhere('req_orders.restaurant_id', $res_id[$i]);
                    }
                })
                ->whereNotNull('req_orders.address')
                ->groupBy('req_orders.restaurant_id')
                ->get();
        }else {
            $results = DB::select('
                SELECT MAX(req_order_status_logs.order_status_id) as ultimo, req_orders.order_id,req_orders.service_type_id as type_id
                FROM req_order_status_logs
                LEFT JOIN req_orders ON req_order_status_logs.order_id = req_orders.order_id
                WHERE  req_orders.service_type_id = 3 AND NOT ISNULL(req_orders.address)
                GROUP BY req_orders.order_id');
        }

        foreach ($results as $value) {
            if(count($res_id) > 0) {
                if (($value->ultimo == 1 && $value->type_id == 1) || ($value->ultimo == 1 && $value->type_id == 2) || ($value->ultimo == 2 && $value->type_id == 3)) {
                    $pending['fillter'] += 1;
                }
                    switch($value->ultimo) {
                        /*case 1: $pending['fillter']  += 1; break;
                        case 2: $pending['fillter'] += 1; break;
                        case 2: $registered['fillter'] += 1; break;*/
                      /*  case 3:
                            $accepted['fillter'] += 1;
                            break;
                        case 4:
                            $dispatched['fillter'] += 1;
                            break;
                        case 5:
                            $delivered['fillter'] += 1;
                            break;
                        case 6:
                            $cancelled['fillter'] += 1;
                            break;
                        case 7:
                            $rejected['fillter'] += 1;
                            break;
                        case 8:
                            $uncollectible['fillter'] += 1;
                            break;
                        default:
                            break;
                    }

            }else{
                if (($value->ultimo == 1 && $value->type_id == 3)) {
                    $pending['fillter'] += 1;
                }

                    switch($value->ultimo){
                        /*case 1: $pending['fillter']  += 1; break;
                        case 2: $pending['fillter'] += 1; break;
                        case 2: $registered['fillter'] += 1; break;*/
                      /*  case 3: $accepted['fillter']  	+= 1; break;
                        case 4: $dispatched['fillter'] += 1; break;
                        case 5: $delivered['fillter'] 	+= 1; break;
                        case 6: $cancelled['fillter'] 	+= 1; break;
                        case 7: $rejected['fillter'] 	+= 1; break;
                        case 8: $uncollectible['fillter'] += 1; break;
                        default: break;
                    }
            }
        }*/
        if($res_id == 0)
        {
            $results = DB::select('
                SELECT MAX(req_order_status_logs.order_status_id) as ultimo, req_orders.order_id,req_orders.service_type_id as type_id
                FROM req_order_status_logs
                LEFT JOIN req_orders ON req_order_status_logs.order_id = req_orders.order_id
                WHERE  req_orders.service_type_id = 3 AND NOT ISNULL(req_orders.address)
                GROUP BY req_orders.order_id');


            foreach ($results as $value) {
                if (($value->ultimo == 1)) {
                    $pending['fillter'] += 1;
                }

                switch($value->ultimo){
                    /*case 1: $pending['fillter']  += 1; break;
                    case 2: $pending['fillter'] += 1; break;
                    case 2: $registered['fillter'] += 1; break;*/
                      case 3: $accepted['fillter']  	+= 1; break;
                      case 4: $dispatched['fillter'] += 1;  break;
                      case 5: $delivered['fillter'] 	+= 1; break;
                      case 6: $cancelled['fillter'] 	+= 1; break;
                      case 7: $rejected['fillter'] 	+= 1; break;
                      case 8: $uncollectible['fillter'] += 1; break;
                      default: break;
                  }
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
          if($staId == 1 || $staId == 2)
          {
              if($res_id == 0) {
                  if($staId == 1) {
                      switch ($val->service_type_id) {
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
                      switch ($val->service_type_id) {
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
                  switch ($val->service_type_id) {
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
          elseif($staId == 3)
          {
              switch ($val->service_type_id) {
                  case 1: $accepted['delivery'] += 1; break;
                  case 2: $accepted['pickup']   += 1; break;
                  case 3: $accepted['pidafacil']   += 1; break;
                  default: break;
              }
          }elseif($staId == 4)
          {
              switch ($val->service_type_id) {
                  case 1: $dispatched['delivery'] += 1; break;
                  case 2: $dispatched['pickup']   += 1; break;
                  case 3: $dispatched['pidafacil']   += 1; break;
                  default: break;
              }
          }
          elseif($staId == 5)
          {
              switch ($val->service_type_id) {
                  case 1: $delivered['delivery'] += 1; break;
                  case 2: $delivered['pickup']   += 1; break;
                  case 3: $delivered['pidafacil']   += 1; break;
                  default: break;
              }
          }
          elseif($staId == 6)
          {
              switch ($val->service_type_id) {
                  case 1: $cancelled['delivery'] += 1; break;
                  case 2: $cancelled['pickup']   += 1; break;
                  case 3: $cancelled['pidafacil']   += 1; break;
                  default: break;
              }
          }
          elseif($staId == 7)
          {
              switch ($val->service_type_id) {
                  case 1: $rejected['delivery'] += 1; break;
                  case 2: $rejected['pickup']   += 1; break;
                  case 3: $rejected['pidafacil']   += 1; break;
                  default: break;
              }
          }
          elseif($staId == 8)
          {
              switch ($val->service_type_id) {
                  case 1: $uncollectible['delivery']  += 1; break;
                  case 2: $uncollectible['pickup']    += 1; break;
                  case 3: $uncollectible['pidafacil']       += 1; break;
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
      $stats['pending']['fillter'] = $stats['pending']['delivery'] + $stats['pending']['pickup'] + $stats['pending']['pidafacil'];
      $stats['registered']['fillter'] = $stats['registered']['delivery'] + $stats['registered']['pickup'] + $stats['registered']['pidafacil'];
      $stats['accepted']['fillter'] = $stats['accepted']['delivery'] + $stats['accepted']['pickup'] + $stats['accepted']['pidafacil'];
      $stats['dispatched']['fillter'] = $stats['dispatched']['delivery'] + $stats['dispatched']['pickup'] + $stats['dispatched']['pidafacil'];
      $stats['delivered']['fillter'] = $stats['delivered']['delivery'] + $stats['delivered']['pickup'] + $stats['delivered']['pidafacil'];
      $stats['cancelled']['fillter'] = $stats['cancelled']['delivery'] + $stats['cancelled']['pickup'] + $stats['cancelled']['pidafacil'];
      $stats['rejected']['fillter'] = $stats['rejected']['delivery'] + $stats['rejected']['pickup'] + $stats['rejected']['pidafacil'];
      $stats['uncollectible']['fillter'] = $stats['uncollectible']['delivery'] + $stats['uncollectible']['pickup'] + $stats['uncollectible']['pidafacil'];

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
		from pidafacil.req_orders as orders 
		inner join pidafacil.req_orders_det as details on orders.order_id = details.order_id
		inner join pidafacil.res_products as product on details.product_id = product.product_id
		where orders.order_id = ?', array( $id));

		foreach ($req_products as $product) {

			$data['producto['.$n.']']=$product;
			
			$ingredient = DB::select('select resingredient.ingredient_id, resingredient.ingredient,reqingredient.remove from pidafacil.req_product_ingredients reqingredient 
			inner join pidafacil.res_ingredients resingredient on reqingredient.ingredient_id =  resingredient.ingredient_id
			where reqingredient.order_det_id = ? and reqingredient.remove != 0',array($product->order_det_id));
			$data['ingredients['.$n.']'] = $ingredient;


			$optioncondition = DB::select('select  orderdetail.order_det_id,conditions.condition,conditionoption.condition_option    from pidafacil.req_orders_det orderdetail
			inner join pidafacil.req_product_conditions_options prodcondition on orderdetail.order_det_id = prodcondition.order_det_id
			inner join pidafacil.res_conditions conditions on conditions.condition_id  =  prodcondition.condition_id
			inner join pidafacil.res_product_conditions_options conditionoption on conditionoption.condition_option_id = prodcondition.condition_option_id 
			where orderdetail.order_det_id = ?',array($product->order_det_id));
			$data['optioncondition['.$n.']'] = $optioncondition;


			$n++;
			
		}
		$data['info'] = DB::select('select orders.restaurant_id,user.user_id, user.email, user.name, user.last_name,orders.order_id, orders.order_total, orders.address_id, address.address_1,
			address.address_2, address.address_name, address.city, address.country_id, address.state, 
			orders.order_status_id, order_status.order_status, orders.service_type_id, service.service_type,
			orders.payment_method_id, payment.payment_method, orders.created_at, orders.updated_at from pidafacil.req_orders as orders 
			inner join pidafacil.diner_addresses as address on orders.address_id = address.address_id
			inner join pidafacil.req_order_status as order_status on orders.order_status_id = order_status.order_status_id
			inner join pidafacil.res_service_types as service on orders.service_type_id = service.service_type_id
			inner join pidafacil.res_payment_methods as payment on orders.payment_method_id =  payment.payment_method_id
			inner join pidafacil.com_users as user on address.user_id = user.user_id
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
            if(isset($result[0])) {
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
                     default:
                         $order_stat = null;
                         break;
                 }
             }

            $restaurants = Restaurant::where('orders_allocator_id',Input::get('id_rest'))->get();

            //$restaurant =  Restaurant::findOrFail(Input::get('id_rest'));

            $orders = Order::where(function($query) use ($restaurants){
                foreach($restaurants as $k=>$v){
                    $query->orWhere('restaurant_id',$v->restaurant_id);
                }
            })
                ->whereNotNull('address')
                ->with('statusLogs')->with('serviceType')->with('paymentMethod')
                ->with('products')->with('users')
                ->orderBy('req_orders.created_at', 'asc')->get();

            $usuarioId = OrderRating::select('user_id')->where('order_id', Input::get("idA"))->get();

           /* foreach($orders as $k => $valuestatus)
            {
                foreach($valuestatus->statusLogs as $k2 => $vals)
                {
                    if($vals->order_id == Input::get("idA"))
                    {
                        $idUser = $vals->user_id;
                    }
                }
            }*/
           /* $orders = Order::where('restaurant_id', $restaurant->restaurant_id)
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
                        return Response::json("entra");
                        $idUser = $vals->user_id;
                    }
                }
            }

           */

            /*$orders = Order::where('order_id',Input::get("idA"))
                ->whereNotNull('address')
                ->with('statusLogs')->with('serviceType')->with('paymentMethod')
                ->with('products')->with('users')
                ->orderBy('req_orders.created_at', 'asc')->get();
            $idUser = $orders[0]->users[0]->user_id;*/

/*Correo electronico... */
            $email = DB::Table('com_users')
                ->select('email', 'name', 'last_name')
                ->where('user_id',$usuarioId[0]->user_id)
                ->get();

            $orderCard  = Order::where('order_id',Input::get("idA"))->get();
            $total      = $orderCard[0]->order_total;
            $detOrder   = OrderDetail::where('order_id',Input::get("idA"))->get();
            if($orderCard[0]->service_type_id == 1)
            {
                $tipoSer = 'Domicilio';
            }elseif($orderCard[0]->service_type_id == 2)
            {
                $tipoSer = 'Para llevar';
            }elseif($orderCard[0]->service_type_id == 3)
            {
                $tipoSer = 'Domicilio pidafacil';
            }else{

                $tipoSer = '';
            }
            if(isset($email[0]))
            {
                $value = array(
                    'user_name' => $email[0]->name." ".$email[0]->last_name,
                    'order_cod' => $order->order_cod,
                    'status'    => 3,
                    'total'     => $total,
                    'productos' => $detOrder,
                    'tipoSer'   => $tipoSer,
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
            //Cancelar la orden
            $order = Order::findOrFail(Input::get("idA"));
            $result = DB::select('
                SELECT MAX(req_order_status_logs.order_status_id) as ultimo
                FROM req_order_status_logs WHERE order_id = ?', array($order->order_id));
            $motorista_asignado = DB::select('SELECT motorista_id FROM req_order_motorista WHERE order_id = ?',array($order->order_id));

            if ($result[0]->ultimo >= 1 ) {
                $cancel = Input::get('rejected');
                if ($cancel >= 5 && $cancel <= 8) {
                    $order_stat = new OrderStatusLog;
                    $order_stat->order_id = $order->order_id;
                    $order_stat->user_id = Auth::id();
                    $order_stat->order_status_id = Input::get('rejected');
                    $order_stat->comment = Input::get('comment');
                    $order_stat->save();
                }elseif ($cancel == 2) {
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
                if(isset($cancel) && $cancel > 4)
                {
                    if(isset($motorista_asignado) && count($motorista_asignado) > 0 ) {
                        $moto = Motorista::find($motorista_asignado[0]->motorista_id);
                        $moto->estado = 0;
                        $moto->save();
                    }
                }
            }
        }
        if(Input::get('id_rest')>0)
        {
            $id_res = Input::get('id_rest');
        }else{
            $id_res = $order->restaurant_id;
        }
        $restaurant =  Restaurant::findOrFail($id_res);
    if(Input::get('id_rest') > 0)
    {
        $orders = Order::where('restaurant_id', $restaurant->restaurant_id)
        ->whereNotNull('address')
        ->with('statusLogs')->with('serviceType')->with('paymentMethod')
        ->with('products')->with('users')
        ->orderBy('req_orders.created_at', 'asc')->get();
    }else{
        $orders = Order::where('service_type_id', 3)
            ->whereNotNull('address')
            ->with('statusLogs')->with('serviceType')->with('paymentMethod')
            ->with('products')->with('users')
            ->orderBy('req_orders.created_at', 'asc')->get();
        //Se envia al despachador del restaurante

      /*  $res_delivery_pidafacil = DB::table('res_restaurants')
            ->select('restaurant_id')
            ->where('orders_allocator_id',$restaurant->orders_allocator_id)
            ->where("delivery_pidafacil",1)
            ->get();*/

        $res_delivery_pidafacil = DB::table('res_restaurants')
            ->select('restaurant_id')
            ->where('orders_allocator_id',$restaurant->orders_allocator_id)
            ->get();

        $o = Order::find(Input::get("idA"));
        $o->restaurant_id = $res_delivery_pidafacil[0]->restaurant_id;
        $o->save();
    }

        /*foreach($orders as $k => $valuestatus)
        {
            foreach($valuestatus->statusLogs as $k2 => $vals)
            {
                if($vals->order_id == Input::get("idA"))
                {
                    $idUser = $vals->user_id;
                }
            }
        }*/

        $usuarioId = OrderRating::select('user_id')->where('order_id', Input::get("idA"))->get();
/*Correo electronico... */
        if(Input::get('rejected') == 3 || Input::get('rejected') == 6 || Input::get('rejected') == 7 || Input::get('rejected') == 8)
        {
            $email = DB::Table('com_users')
                ->select('email', 'name', 'last_name')
                ->where('user_id', $usuarioId[0]->user_id)
                ->get();

            $orderCard = Order::where('order_id', Input::get("idA"))->get();
            $total = $orderCard[0]->order_total;

            $detOrder = OrderDetail::where('order_id', Input::get("idA"))->get();

            if ($orderCard[0]->service_type_id == 1) {
                $tipoSer = 'Domicilio';
            } elseif ($orderCard[0]->service_type_id == 2) {
                $tipoSer = 'Para llevar';
            } elseif ($orderCard[0]->service_type_id == 2) {
                $tipoSer = 'Domicilio pidafacil';
            } else {
                $tipoSer = '';
            }

            $value = array(
                'user_name' => $email[0]->name . " " . $email[0]->last_name,
                'order_cod' => $order->order_cod,
                'status' => Input::get('rejected'),
                'motivo' => Input::get('comment'),
                'motivoRechazo' => Input::get('motivoRechazo'),
                'total' => $total,
                'productos' => $detOrder,
                'tipoSer' => $tipoSer,
            );
            $this->email->cambioEstado($email[0]->email, $email[0]->name . " " . $email[0]->last_name, $value);
        }

        if( Input::get('id_rest') > 0)
        {
            $stats = $this->stats($orders,$restaurant->restaurant_id);
        }else{
            $stats = $this->stats($orders,0);
        }

        /*
         * Borrar/actualizar datos de tarjeta de credito.
         *
         * */

        if( Input::get("borrarT") )
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
        }
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
    public function motorista()
    {
        $mot1 = DB::select("SELECT max(motoristas.motorista_id) as 'motorista_id', concat(motoristas.nombre,' ',motoristas.apellido) as 'nombre_motorista'
                                  FROM motoristas where motoristas.motorista_id not in (SELECT motorista_id FROM req_order_motorista) group by motoristas.motorista_id");

        $motoristas = DB::select("SELECT motorista_id,concat(nombre,' ',motoristas.apellido) as 'nombre_motorista' FROM motoristas WHERE estado = 0 order by motorista_id ASC");

        if(isset($mot1[0]) && $mot1[0]->motorista_id != null)
        {
            if(isset($motoristas[0]) )
            {
                if(count($motoristas) >= count($mot1))
                {
                    for($i = 0; $i < count($motoristas); $i++)
                    {
                        for($j = 0; $j < count($mot1); $j++){
                            if($motoristas[$i]->motorista_id == $mot1[$j]->motorista_id)
                            {
                                unset($motoristas[$i]);
                                $i++;
                            }
                        }
                    }
                }else{
                    for($j = 0; $j < count($mot1); $j++){
                        for($i = 0; $i < count($motoristas); $i++)
                        {
                            if($motoristas[$i]->motorista_id == $mot1[$j]->motorista_id)
                            {
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

    public function deliveryPidafacil()
    {
        $limit = Order::where('service_type_id',3)->whereNotNull('address')->count();
        $or = Order::where('service_type_id',3)->whereNotNull('address')->with('motoristas')->get();
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
        /*
        $results = DB::select('select orders.restaurant_id,user.user_id, user.email, user.name, user.last_name,orders.order_id, orders.order_total, orders.address_id, address.address_1,
            address.address_2, address.address_name, address.city, address.country_id, address.state,
            orders.order_status_id, order_status.order_status, orders.service_type_id, service.service_type,
            orders.payment_method_id, payment.payment_method, orders.created_at, orders.updated_at from pidafacil.req_orders as orders
            inner join pidafacil.diner_addresses as address on orders.address_id = address.address_id
            inner join pidafacil.req_order_status as order_status on orders.order_status_id = order_status.order_status_id
            inner join pidafacil.res_service_types as service on orders.service_type_id = service.service_type_id
            inner join pidafacil.res_payment_methods as payment on orders.payment_method_id =  payment.payment_method_id
            inner join pidafacil.com_users as user on address.user_id = user.user_id
            where orders.restaurant_id = ? and order_status.order_status_id = 1 order by orders.created_at desc;', array($id));
        */

        //Verifica si el restarante tiene mas de un despachador/orders_allocator_id entre sus sucursales.

        $MasDeUnDespacho = array();
        if($limit > 0) {
            foreach ($orders as $k => $v) {
                $restaruantsHijos = Restaurant::where('parent_restaurant_id', $v->id)->get();
                $MasDeUnDespacho[$v->id] = true;
                if (isset($restaruantsHijos[0])) {
                    $alocatorId1 = $restaruantsHijos[0]->orders_allocator_id;
                    if (count($restaruantsHijos) > 0) {
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

        if(empty($motoristas))
        {
            return View::make('web.delivery_pidafacil')
                ->with('orders', $orders)
                ->with('stats', $stats)
                ->with('condi',$condi)
                ->with('MasDeUnDespacho',$MasDeUnDespacho)
                ->with('res', $res);
        }else{
            return View::make('web.delivery_pidafacil')
                ->with('orders', $orders)
                ->with('stats', $stats)
                ->with('condi',$condi)
                ->with('motoristas',$motoristas)
                ->with('MasDeUnDespacho',$MasDeUnDespacho)
                ->with('res', $res);
        }
    }

    public  function  time()
    {
        $result = DB::select('
                SELECT order_status_id, created_at
                FROM req_order_status_logs WHERE order_id = ? group by req_order_status_logs.order_status_id DESC limit 1' , array(Input::get("id")));
        return  Response::json($result);
    }

}