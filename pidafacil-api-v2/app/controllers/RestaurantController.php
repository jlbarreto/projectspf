<?php

class RestaurantController extends \BaseController {

  /**
  * Display a listing of the resource.
  * GET /restaurant
  *
  * @return Response
  */
  public function index() {
    // Show restaurant list by category
    $input = Input::all();
    try {
      $statusCode = 200;

      $tag = Tag::find($input['tag_id']);

      if (isset($input['page_size']) && $input['page_size'] > 0) {
        $pagesze = $input['page_size'];
        $pagepos = $input['page_post'];

        $rests = $tag->restaurants()
        ->whereRaw('parent_restaurant_id = res_restaurants.restaurant_id')
        ->where('activate', 1)
        ->orderBy('created_at', 'asc')
        ->with('landingPage')
        ->take($pagesze)->skip($pagepos * $pagesze)
        ->get();
      } else {
        $rests = $tag->restaurants()
        ->whereRaw('parent_restaurant_id = res_restaurants.restaurant_id')
        ->orderBy('created_at', 'asc')
        ->where('activate', 1)
        ->with('landingPage')
        ->get();
      }

      foreach ($rests as $rest) {
        $rest->tag_name = $tag->tag_name;

        $rest->landing_page->text_1 = $this->shortText($rest->landing_page->text_1);
      }

      if ($rests->count() > 0) {
        $response['status'] = true;
        $response['data'] = $rests;
      } else {
        $response = array(
          "status" => false,
          "data" => 'No hay restaurantes en esta categoria.'
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
  * Muestra todos los restaurantes existentes.
  * GET /restaurant/list
  *
  * @return Response
  */
  public function restaurants_list() {

    try {

      $statusCode = 200;

      $rests = Restaurant::with('landingPage')
      ->whereRaw('parent_restaurant_id = restaurant_id')
      ->where('activate', 1)
      ->orderBy('position', 'asc')
      ->get();

      foreach ($rests as $rest) {
        $rest->landing_page->text_1 = $this->shortText($rest->landing_page->text_1);
      }

      if ($rests->count() > 0) {
        $response['status'] = true;
        $response['data'] = $rests;
      } else {
        $response = array(
          "status" => false,
          "data" => 'No hay restaurantes.'
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

  private function shortText($str) {
    if (strlen($str) > 180) {
      $str = substr($str, 0, 180);

      $pos = strrpos($str, '.');

      if (!($pos === false)) {
        $str = substr($str, 0, $pos + 1);
      }
    }

    return $str;
  }

  /**
  * Show the form for creating a new resource.
  * GET /restaurant/create
  *
  * @return Response
  */
  public function create() {
    //
  }

  /**
  * Store a newly created resource in storage.
  * POST /restaurant
  *
  * @return Response
  */
  public function store() {
    //
  }

  /**
  * Display the specified resource.
  * GET /restaurant/{id}
  *
  * @param  int  $id
  * @return Response
  */
  public function show() {
    // Show restaurant detail
    $input = Input::all();
    try {
      $statusCode = 200;
      $resadds = array();

      $rest = Restaurant::findOrFail($input['restaurant_id']);
      $rest->schedules = Restaurant::find($rest->restaurant_id)->schedules;
      $rest->landing_page = Restaurant::find($rest->parent_restaurant_id)->landingPage;

      $rest->min_price = DB::table('res_products')
      ->leftJoin('res_sections', 'res_products.section_id', '=', 'res_sections.section_id')
      ->leftJoin('res_restaurants', 'res_sections.restaurant_id', '=', 'res_restaurants.restaurant_id')
      ->where('res_restaurants.restaurant_id', $rest->parent_restaurant_id)
      ->groupBy('res_restaurants.restaurant_id')->min('res_products.value');
      $rest->max_price = DB::table('res_products')
      ->leftJoin('res_sections', 'res_products.section_id', '=', 'res_sections.section_id')
      ->leftJoin('res_restaurants', 'res_sections.restaurant_id', '=', 'res_restaurants.restaurant_id')
      ->where('res_restaurants.restaurant_id', $rest->parent_restaurant_id)
      ->groupBy('res_restaurants.restaurant_id')->max('res_products.value');

      $addrs = Restaurant::where("parent_restaurant_id", $rest->parent_restaurant_id)->get();
      foreach ($addrs as $key => $value) {
        if ($value->restaurant_id != $rest->restaurant_id) {
          $resadds[] = array(
            "restaurant_id" => $value->restaurant_id,
            "address" => $value->name . " - " . $value->address
          );
        }
      }
      if (count($resadds) > 0) {
        $rest->branches = $resadds;
      } else {
        $rest->branches = null;
      }


      if (!empty($rest)) {
        $response['status'] = true;
        $response['data'] = $rest;
      } else {
        $response = array(
          "status" => false,
          "data" => 'No existe el restaurante solicitado.'
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
  * Show the form for editing the specified resource.
  * GET /restaurant/{id}/edit
  *
  * @param  int  $id
  * @return Response
  */
  public function edit($id) {
    //
  }

  /**
  * Update the specified resource in storage.
  * PUT /restaurant/{id}
  *
  * @param  int  $id
  * @return Response
  */
  public function update($id) {
    //
  }

  /**
  * Remove the specified resource from storage.
  * DELETE /restaurant/{id}
  *
  * @param  int  $id
  * @return Response
  */
  public function destroy($id) {
    //
  }

  /**
  * Show restaurants categories from storage.
  * POST /restaurant/categories
  *
  * @param  int  $tag_type_id
  * @return Response
  */
  public function categories() {
    // Show category list
    $input = Input::all();
    try {
      $statusCode = 200;

      if (is_array($input['tag_type_id'])) {
        $tags = Tag::whereIn('tag_type_id', $input['tag_type_id'])
        ->with('restaurants')->orderBy('position', 'desc')->get();
      } else {
        $tags = Tag::where('tag_type_id', $input['tag_type_id'])
        ->with('restaurants')->orderBy('position', 'desc')->get();
      }

      $final_tags = array();

      for ($i = count($tags) - 1; $i >= 0; $i--) {
        //Sino tiene restaurante eliminar
        if (count($tags[$i]->restaurants) > 0) {
          $agregar = true;
          //Si es promocion y no tiene promociones eliminar
          if(isset($input['promos']) and $input['promos']){
            $agregar = false;

            foreach($tags[$i]->restaurants as $restaurant){
              $n = $restaurant->products()->where('promotion', true)->count();

              if($n>0){
                $agregar=true;
              }
            }
          }

          unset($tags[$i]->restaurants);

          if($agregar){
            $final_tags[] = $tags[$i];
          }
        }
      }


      if (count($tags) > 0) {
        $response['status'] = true;
        $response['data'] = $final_tags;
      } else {
        $response = array(
          "status" => false,
          "data" => 'Categorias no encontradas.'
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
  * Display a listing of the resource by promos.
  * GET /restaurant/promos
  *
  * @return Response
  */
  public function promos() {
    // Show restaurant list by promotions
    $input = Input::all();
    try {
      $statusCode = 200;
      if (!empty($input['tag_id'])) {
        if (isset($input['page_size']) && $input['page_size'] > 0) {
          $pagesze = $input['page_size'];
          $pagepos = $input['page_post'];
          $rests = Tag::findOrFail($input['tag_id'])
          ->restaurants()->with('landingPage')
          ->whereHas('products', function($q) {
            $date = date('Y-m-d H:i:s');
            $q->where('promotion', 1)
              ->where('start_date', '<=', $date)
              ->where('end_date', '>=', $date)
            ->orderBy('res_products.created_at', 'desc');
          })
          ->take($pagesze)->skip($pagepos * $pagesze)
          ->get();
        } else {
          $rests = Tag::findOrFail($input['tag_id'])
          ->restaurants()->with('landingPage')
          ->whereHas('products', function($q) {
            $date = date('Y-m-d H:i:s');
            $q->where('promotion', 1)
              ->where('start_date', '<=', $date)
              ->where('end_date', '>=', $date)
            ->orderBy('res_products.created_at', 'desc');
          })->get();
        }
      } else {
        if (isset($input['page_size']) && $input['page_size'] > 0) {
          $pagesze = $input['page_size'];
          $pagepos = $input['page_post'];
          $rests = Restaurant::with('landingPage')
          ->whereHas('products', function($q) {
            $date = date('Y-m-d H:i:s');
            $q->where('promotion', 1)
              ->where('start_date', '<=', $date)
              ->where('end_date', '>=', $date)
            ->orderBy('res_products.created_at', 'desc');
          })
          ->take($pagesze)->skip($pagepos * $pagesze)
          ->get();
        } else {
          $rests = Restaurant::with('landingPage')
          ->whereHas('products', function($q) {
            $date = date('Y-m-d H:i:s');
            $q->where('promotion', 1)
              ->where('start_date', '<=', $date)
              ->where('end_date', '>=', $date)
              ->orderBy('res_products.created_at', 'desc');
          })->get();
        }
      }

      foreach($rests as $rest){
        $rest->landing_page->text_1 = $this->shortText($rest->landing_page->text_1);
      }

      if ($rests->count() > 0) {
        $response['status'] = true;
        $response['data'] = $rests;
      } else {
        $response = array(
          "status" => false,
          "data" => 'No hay Restaurantes con promociones.'
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
  * Show restaurants sections from storage.
  * POST /restaurant/sections
  *
  * @param  int  $restaurant_id
  * @return Response
  */
  public function sections($restaurant) {
    // Show restaurant sections
    $input = Input::all();
    try {
      $statusCode = 200;
      $sections = Section::where('restaurant_id', $restaurant)
      ->where('activate', true)
      ->get();
      if (!empty($sections)) {
        //$response['status'] = true;
        $response['data'] = $sections;
      } else {
        $response = array(
          //"status" => false,
          "data" => 'No hay menu en el restaurante solicitado.'
        );
      }
    } catch (Exception $e) {
      $statusCode = 400;
      $response = array(
        //"status" => false,
        "data" => $e->getMessage()
      );
    }
    return Response::json($response, $statusCode);
  }

  /**
  * Show restaurants products section from storage.
  * POST /restaurant/products
  *
  * @param  int  $section_id
  * @return Response
  */
  public function products($secID) {
    // Show restaurant products
    try {
      $statusCode = 200;

      if (isset($secID)) {
        //$pagesze = $input['page_size'];
        //$pagepos = $input['page_post'];
        $products = Product::where('section_id', $secID)
        ->where('activate', true)
        //->take($pagesze)->skip($pagepos * $pagesze)
        ->get();
      } else {
        $products = Product::where('section_id', $secID)
        ->where('activate', true)
        ->get();
      }

      if ($products->count() > 0) {
        $response['status'] = true;
        $response['data'] = $products;
      } else {
        $response = array(
          "status" => false,
          "data" => 'No hay productos en esta seccion.'
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

  public function addresses($restaurant) {
    $input = Input::all();

    try {
      $statusCode = 200;
      $limit = Restaurant::where("parent_restaurant_id", $restaurant)
      ->where("activate", 1)
      ->count();
      if ($limit > 0) {
        $addrs = Restaurant::where("parent_restaurant_id", $restaurant)
        ->where("activate", 1)
        ->get();
        foreach ($addrs as $key => $value) {

          //Solamente cargar cuando sea un solo restaurante, sino sucurusales y no marca
          if ($limit == 1 or $value->restaurant_id != $restaurant) {
            $resadds[] = array(
              "restaurant_id" => $value->restaurant_id,
              "address" => $value->name . " - " . $value->address
            );
          }
        }
        $response['status'] = true;
        $response['data'] = $resadds;
      } else {
        $response = array(
          "status" => false,
          "data" => "No hay direccion de restaurante."
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
  * Display a listing of schedules.
  * GET /restaurant
  *
  * @return Response
  */
  public function schedule() {
    $input = Input::all();
    try {
      $statusCode = 200;
      if (isset($input['service_type_id'])) {
        $limit = Schedule::where('restaurant_id', $input['restaurant_id'])
        ->where('service_type_id', $input['service_type_id'])
        ->where('day_id', date('N')+1)->count();
      } else {
        $limit = Schedule::where('restaurant_id', $input['restaurant_id'])
        ->where('day_id', date('N')+1)->count();
      }

      if ($limit > 0) {
        if (isset($input['service_type_id'])) {
          $schedules = Schedule::where('restaurant_id', $input['restaurant_id'])
          ->where('service_type_id', $input['service_type_id'])
          ->where('day_id', date('N')+1)->first();
        } else {
          $schedules = Schedule::where('restaurant_id', $input['restaurant_id'])
          ->where('day_id', date('N')+1)->first();
        }

        $otime = strtotime($schedules->opening_time);
        $schedules->opening_time = date('G', $otime);
        $ctime = strtotime($schedules->closing_time);
        $schedules->closing_time = date('G', $ctime);

        $response['status'] = true;
        $response['data'] = $schedules;
      } else {
        $response = array(
          "status" => false,
          "data" => 'No hay horarios para este Restaurante.'
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

  public function service_types() {
    $input = Input::all();
    try {
      $statusCode = 200;
      $limit = Restaurant::find($input['restaurant_id'])->serviceTypes->count();
      if ($limit > 0) {
        $sts = Restaurant::find($input['restaurant_id'])->serviceTypes;
        foreach ($sts as $key => $val) {
          if ($val->service_type_id == 3) {
            $val->service_type = "Domicilio";
          }
          $service_types[] = array(
            'service_type_id' => $val->service_type_id,
            'service_type' => $val->service_type);
          }
          $response['status'] = true;
          $response['data'] = $service_types;
        } else {
          $response = array(
            "status" => false,
            "data" => 'No hay tipos de servicio para este Restaurante.'
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

    public function payment_methods() {
      $input = Input::all();
      try {
        $statusCode = 200;
        $limit = Restaurant::find($input['restaurant_id'])->paymentMethods->count();
        if ($limit > 0) {
          $pms = Restaurant::find($input['restaurant_id'])->paymentMethods;
          foreach ($pms as $key => $val) {
            $payment_methods[] = array(
              'payment_method_id' => $val->payment_method_id,
              'payment_method' => $val->payment_method);
            }
            $response['status'] = true;
            $response['data'] = $payment_methods;
          } else {
            $response = array(
              "status" => false,
              "data" => 'No hay metodos de pago para este Restaurante.'
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
      * Metodo que se ejecuta al
      * /restaurant/getInfo
      *
      * @return type
      */
      public function info() {
        $input = Input::all();
        try {
          $statusCode = 200;
          $restaurant = Restaurant::findOrFail($input['restaurant_id']);


          if ($restaurant->restaurant_id == $restaurant->parent_restaurant_id) {
            $web_content = $restaurant->landingPage()->firstOrFail();
          } else {
            $web_content = Restaurant::findOrFail($restaurant->parent_restaurant_id)->landingPage()->firstOrFail();
          }


          $response['status'] = true;
          $response['data'] = array(
            'restaurant_id' => $restaurant->restaurant_id,
            'name' => $restaurant->name,
            'logo_uri' => $web_content->logo
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
      * Metodo que actualiza las coordenadas del restaurante
      * POST /restaurant/update-coordinates
      * @param  int  $restaurant_id
      * @param  string  $map_coordinates
      * @return Response
      */
      public function update_coordinates() {
        $input = Input::all();
        try {
          $statusCode = 200;

          $restaurant = Restaurant::findOrFail($input['restaurant_id']);

          $restaurant->map_coordinates = $input['map_coordinates'];
          $restaurant->save();

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

    }
