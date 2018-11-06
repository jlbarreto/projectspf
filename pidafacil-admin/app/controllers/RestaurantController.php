<?php

class RestaurantController extends \BaseController {

    /**
     * Display a listing of the resource.
     * GET /restaurant
     *
     * @return Response
     */
    public function index() {
        $data['error'] = false;
        $data['data'] = Restaurant::all();
        return $data;
    }

    public function all() {
        #echo $_SERVER['DOCUMENT_ROOT'].'/imagespf/'.'<br>';
        #echo public_path(); die();

        $restaurantsAll = Restaurant::all();
        
        $restaurants = array();
        
        foreach ($restaurantsAll as $r) {
            if($r->restaurant_id==$r->parent_restaurant_id){
                $restaurants[]=$r;
            }
        }
        
        return View::make('admin.restaurant_all')
            ->with('restaurants', $restaurants);
    }

    public function branchs($restslug) {
        $data['parent'] = ParentRestaurant::getParent($restslug);        
        $data['restaurants'] = Restaurant::where('parent_restaurant_id', $data['parent']->restaurant_id)->get();
        
        return View::make('admin.restaurant_branchs')
            ->with($data);
    }

    /**
     * Show the form for creating a new resource.
     * GET /restaurant/create
     *
     * @return Response
     */
    public function create() {
        return View::make('admin.restaurant_create')->with($this->getLoaders());
    }

    private function getLoaders() {
        $services_type = ServiceType::orderBy('service_type', 'DESC')->get();
        $data['payment_methods'] = PaymentMethod::all();

        $data['services_types'] = ServiceType::all();
        $data['tag_types'] = TagType::with('tags')->get();

//        for($i=0; $i<count($data['tag_types']); $i++) {
//            $data['tag_types'][$i]['tags'] = $data['tag_types'][$i]->tags()->get();
//        }

        foreach ($services_type as $service_type) {
            $data['ar_services_type'][$service_type->service_type_id] = $service_type->service_type;
        }

        return $data;
    }

    /**
     * Store a newly created resource in storage.
     * POST /restaurant
     *
     * @return Response
     */
    public function store() {
        if (Input::get('webContent') !== "existe" and Input::get('parent_restaurant_id') == 0) {
            return Redirect::back()
                ->withErrors("Si el restaurante no tiene un restaurante padre debe definirse un web content")
                ->withInput();
        } else {
            $input = Input::all();

            $validacion = Restaurant::validacion($input); //Manejo de validación desde el modelo

            if ($validacion->fails()) {//comprobando el resultado de la validación
                Session::flash('flash_message', 'Si seleccionó un archivo para subir tiene que seleccionarlo nuevamente');
                return Redirect::back()
                    ->withErrors($validacion)
                    ->withError("No subió")
                    ->withInput(); //Si existe algún error se retorna todos los errores
            } else {

                if (Input::get('webContent') === "existe") {

                    $validacion2 = Webcontent::validacion($input);

                    if ($validacion2->fails()) {
                        Session::flash('flash_message', 'Si seleccionó un archivo para subir tiene que seleccionarlo nuevamente');
                        return Redirect::back()
                                        ->withErrors($validacion2)
                                        ->withInput();
                    } else {
                        $restaurant = $this->saveRestaurant();
                        $this->saveWebContent($restaurant);
                    }
                } else {
                    $restaurant = $this->saveRestaurant();
                }
                
                return Redirect::to('admin/restaurant/define_horarios/' . $restaurant->slug);
            }
        }
    }

    private function saveRestaurant() {
        $restaurant = Restaurant::create(Input::all());
        $restaurant->days_as_new = 30;
        $restaurant->search_reserved_position = 0;

        $restaurant->save();


        if (Input::get('parent_restaurant_id') == 0) { //Si es cero será el mismo
            $restaurant->parent_restaurant_id = $restaurant->restaurant_id;
        } else {
            $restaurant->parent_restaurant_id = Input::get('parent_restaurant_id');
        }

        if (Input::get('orders_allocator_id') == 0) { //Si es cero será el mismo
            $restaurant->orders_allocator_id = $restaurant->restaurant_id;
        } else {
            $restaurant->orders_allocator_id = Input::get('orders_allocator_id');
        }

        $restaurant->commission_percentage = Input::get('commission_percentage');
        
        $restaurant->save();

        $this->saveSyncs($restaurant);

        Session::flash('flash_message', 'Restaurante agregado con éxito, por favor defina el horario');

        Session::put('wizard', true);
        Session::put('step', 1);
        Session::put('restaurant_slug', $restaurant->slug);
        
        return $restaurant;
    }

    private function saveSyncs(Restaurant $restaurant) {
        $restaurant->services_types()->sync((Input::has('service_types')) ? Input::get('service_types') : array());
        $restaurant->tags()->sync((Input::has('tags')) ? Input::get('tags') : array());

        if (Input::has('payment_methods')) {
            $restaurant->paymentmethods()->sync(Input::get('payment_methods'));
        } else {
            $restaurant->paymentmethods()->sync(array(1));
        }
        
        //Para los contactos
        $n = count(Input::get('contact_id'));
        
        $lst = array();
        
        for($i=1; $i<=$n; $i++){
            $id=Input::get('contact_id')[$i];
            
            if($id!=null){
                $contact = Contact::findOrFail($id);
            }else{
                $contact = new Contact();
            }
            
            $contact->contact_name=Input::get('contact_name')[$i];
            $contact->contact_celular = Input::get('contact_celular')[$i];
            $contact->contact_phone = Input::get('contact_phone')[$i];
            $contact->contact_email = Input::get('contact_email')[$i];
            $contact->save();
            
            $lst[] = $contact->contact_id;
        }
        
        $restaurant->contacts()->sync($lst);
    }

    private function saveWebContent(Restaurant $restaurant, $id = NULL) {

        $webcontent = new Webcontent;

        if ($id != null) {
            $webcontent = Webcontent::find($id);
        }

        //$destino = '/var/www/html/pidafacil/imagespf/';
        $destino = '/var/www/pidafacil/imagespf/';

        $destinationPath = 'restaurants/' . $restaurant->slug . '/';

        if (Input::hasFile('header')) {
            $header = Input::file('header');
            $nombre = 'header.' . $header->getClientOriginalExtension();
            //$uploadSuccess = $header->move(public_path() . Config::get('app.image_position_from_public'). $destinationPath, $nombre);
            $uploadSuccess = $header->move($destino.''.$destinationPath, $nombre);

            if ($uploadSuccess)
                $webcontent->header = '/'.$destinationPath . $nombre;
            else
                Session::flash('flash_message', 'Error al subir el header');
        }

        if (Input::hasFile('logo')) {
            $logo = Input::file('logo');
            $nombre = 'logo.' . $logo->getClientOriginalExtension();
            #$uploadSuccess = $logo->move(public_path() . Config::get('app.image_position_from_public').$destinationPath, $nombre);
            $uploadSuccess = $logo->move($destino.''.$destinationPath, $nombre);

            if ($uploadSuccess)
                $webcontent->logo = '/'.$destinationPath . $nombre;
            else
                Session::flash('flash_message', 'Error al subir el logo');
        }

        if (Input::hasFile('banner')) {
            $banner = Input::file('banner');
            $nombre = 'banner.' . $banner->getClientOriginalExtension();
            #$uploadSuccess = $banner->move(public_path() . Config::get('app.image_position_from_public'). $destinationPath, $nombre);
            $uploadSuccess = $banner->move($destino.''.$destinationPath, $nombre);

            if ($uploadSuccess)
                $webcontent->banner = '/'.$destinationPath . $nombre;
            else
                Session::flash('flash_message', 'Error al subir el bannner');
        }
        
        $webcontent->slogan = Input::get('slogan');
        $webcontent->title_1 = Input::get('title_1');
        $webcontent->title_2 = Input::get('title_2');
        $webcontent->title_3 = Input::get('title_3');
        $webcontent->text_1 = Input::get('text_1');
        $webcontent->text_2 = Input::get('text_2');
        $webcontent->text_3 = Input::get('text_3');
        $webcontent->save();

        $restaurant->landing_page_id = $webcontent->landing_page_id;
        $restaurant->save();
    }

    /**
     * Display the specified resource.
     * GET /{restslug}/
     *
     * @param  string  $restslug
     * @return Response
     */
    public function show($restslug) {
        $restaurant = ParentRestaurant::getParent($restslug);
        $data['informacion'] = $restaurant;
        $data['landingpage'] = $restaurant->landingpage()->get();
        $data['sections'] = $restaurant->sections()->where('activate', '=',  true)->orderBy('section_order_id', 'asc')->get();
        return View::make('web.restaurant', $data);
    }

    /**
     * Show the form for editing the specified resource.
     * GET /{restslug}/edit
     *
     * @param  string  $restslug
     * @return Response
     */
    public function edit($restslug) {
        $data = $this->getLoaders();
        $restaurant = Restaurant::where('slug', $restslug)->firstOrFail();
        
        $data['restaurant'] = $restaurant;
        $data['services_type'] = $restaurant->services_types()->get();
        $data['payment_methods_selected'] = $restaurant->paymentmethods()->get();
        $data['tags'] = $restaurant->tags()->get();

        if ($restaurant->landing_page_id != 0) {
            $data['webcontent'] = $restaurant->landingpage()->get()[0];
        } else {
            $data['webcontent'] = new Webcontent();
        }


        //Get parent
        if ($restaurant->parent_restaurant_id != $restaurant->restaurant_id) {
            $parent = Restaurant::find($restaurant->parent_restaurant_id);
        } else {
            $parent = $restaurant;
        }
        $data['parent'] = $parent;
        
        if ($restaurant->orders_allocator_id == $parent->restaurant_id) {
            $data['orders_allocator'] = $parent;
        } else if ($restaurant->orders_allocator_id == $restaurant->restaurant_id) {
            $data['orders_allocator'] = $restaurant;
        } else {
            $data['orders_allocator'] = Restaurant::find($restaurant->orders_allocator_id);
        }
        
        $data['contacts'] = $restaurant->contacts()->get();

//        $data['error'] = false;
//        $data['restaurant'] = Restaurant::where('slug', $restslug)->firstOrFail();
//        //********************************************
//        //*      Falta verificar si el usuario       *
//        //*      posee permisos para editar el       *
//        //*		        RESTAURANTE                  *
//        //********************************************
//        $data['message'] = 'Formulario para edicion de ' . $data['restaurant']->name . ' ';
        return View::make('admin.restaurant_edit')->with($data);
    }

    public function define_horarios($restslug) {
        $restaurant = Restaurant::where('slug', $restslug)->firstOrFail();

        $data['servicios'] = $restaurant->services_types()->get();
        $schedules = $restaurant->schedules()->get();

        $data['arr_schedules'] = array();
        foreach ($schedules as $schedule) {
            $trunks_a = explode(':', $schedule->opening_time);
            $data['arr_schedules'][] = array('dia' => $schedule->day_id, 'type' => 'a', 'service' => $schedule->service_type_id, 'hora' => $trunks_a[0], 'minuto' => $trunks_a[1]);
            $trunks_c = explode(':', $schedule->closing_time);
            $data['arr_schedules'][] = array('dia' => $schedule->day_id, 'type' => 'c', 'service' => $schedule->service_type_id, 'hora' => $trunks_c[0], 'minuto' => $trunks_c[1]);
        }

        $data['restaurant'] = $restaurant;
        $data['arrDias'] = $this->arrDias();
        return View::make('admin.restaurant_horario')->with($data);
    }

    public function set_horarios($restslug) {
        $restaurant = Restaurant::where('slug', $restslug)->firstOrFail();
        $services = $restaurant->services_types()->get();

        //Eliminando los que ya tiene
        $existentes = $restaurant->schedules()->get();
        foreach ($existentes as $ant) {
            $ant->delete();
        }

        foreach ($services as $service) {
            if (Input::has($service->service_type_id . '_dias')) {
                $dias = Input::get($service->service_type_id . '_dias');
                foreach ($dias as $v) {
                    $schedule = new Schedule;

                    $schedule->restaurant_id = $restaurant->restaurant_id;
                    $schedule->day_id = $v;
                    $schedule->opening_time = Input::get($service->service_type_id . '_hora_a_' . $v) . ':' . Input::get($service->service_type_id . '_minuto_a_' . $v) . ':00';
                    $schedule->closing_time = Input::get($service->service_type_id . '_hora_c_' . $v) . ':' . Input::get($service->service_type_id . '_minuto_c_' . $v) . ':00';
                    $schedule->service_type_id = $service->service_type_id;
                    $schedule->save();
                }
            }
        }

        Session::flash('flash_message', 'Almacenados los horarios');

        if(Session::has('wizard')){
            Session::put('step', 2);
            return Redirect::to('admin/restaurant/sections/edit/'.Session::get('restaurant_slug'));
        }else{
            return Redirect::back();
        }
    }

    public static function arrDias() {
        return array(1 => 'Domingo', 2 => 'Lunes', 3 => 'Martes', 4 => 'Miércoles', 5 => 'Jueves', 6 => 'Viernes', 7 => 'Sábado');
    }

    /**
     * Update the specified resource in storage.
     * POST /{restslug}/update
     *
     * @param  string $restslug
     * @return Response
     */
    public function update($id) {
        if (Input::get('webContent') !== "existe" and Input::get('parent_restaurant_id') == 0) {
            Session::flash('flash_message', 'Si seleccionó un archivo para subir tiene que seleccionarlo nuevamente');
            return Redirect::back()
                            ->withErrors("Si el restaurante no tiene un restaurante padre debe definirse un web content")
                            ->withInput();
        } else {
            $validacion = Restaurant::validacion(Input::all());

            if ($validacion->fails()) {
                Session::flash('flash_message', 'Si seleccionó un archivo para subir tiene que seleccionarlo nuevamente');
                return Redirect::back()
                                ->withErrors($validacion)
                                ->withInput();
            } else {
                if (Input::get('webContent') === "existe") {

                    $validacion2 = Webcontent::validacion(Input::all());

                    if ($validacion2->fails()) {
                        Session::flash('flash_message', 'Si seleccionó un archivo para subir tiene que seleccionarlo nuevamente');
                        return Redirect::back()
                                        ->withErrors($validacion2)
                                        ->withInput();
                    } else {
                        $restaurant = $this->updateRestaurant($id);
                        $this->saveWebContent($restaurant, $restaurant->landing_page_id);
                    }
                } else {
                    $restaurant = $this->updateRestaurant($id);

                    if ($restaurant->landing_page_id != 0) {
//                    No elimina el webcontent porque no se sabe si se debe eliminar
//                    $webContent = Webcontent::find($restaurant->landing_page_id);
//                    $webContent->delete();
                        $restaurant->landing_page_id = null;
                        $restaurant->save();
                    }
                }
            }

            Session::flash('flash_message', 'Restaurante actualizado con éxito');
            return Redirect::back();
        }
    }

    private function updateRestaurant($id) {
        $restaurant = Restaurant::find($id);
        $restaurant->name = Input::get('name');
        $restaurant->orders_allocator_id = Input::get('orders_allocator_id');
        $restaurant->guarantee_time = Input::get('guarantee_time');
        $restaurant->parent_restaurant_id = Input::get('parent_restaurant_id');
        //$restaurant->landing_page_id = Input::get('landing_page_id');
        $restaurant->delivery_time = Input::get('delivery_time');
        $restaurant->shipping_cost = Input::get('shipping_cost');
        $restaurant->minimum_order = Input::get('minimum_order');
        $restaurant->phone = Input::get('phone');
        $restaurant->address = Input::get('address');
        $restaurant->map_coordinates = Input::get('map_coordinates');
        $restaurant->commission_percentage = Input::get('commission_percentage');
        $restaurant->search_reserved_position = 0;
        $restaurant->days_as_new = 30;
        $restaurant->save();
        
        


        if (Input::get('parent_restaurant_id') == 0) { //Si es cero será el mismo
            $restaurant->parent_restaurant_id = $restaurant->restaurant_id;
        } else {
            $restaurant->parent_restaurant_id = Input::get('parent_restaurant_id');
        }

        if (Input::get('orders_allocator_id') == 0) { //Si es cero será el mismo
            $restaurant->orders_allocator_id = $restaurant->parent_restaurant_id;
        } else {
            $restaurant->orders_allocator_id = Input::get('orders_allocator_id');
        }

        $this->saveSyncs($restaurant);

        return $restaurant;
    }

    /**
     * Remove the specified resource from storage.
     * DELETE /restaurant/{id}
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id) {

        //No hacer nada
//        $restaurant = Restaurant::find($id);
//
//        $restaurant->delete();
//        
//        Session::flash('flash_message', 'Restaurante Eliminado');

        return Redirect::to('admin/restaurant/list');
    }

    public function desactivate($id) {

        $restaurant = Restaurant::find($id);

        $restaurant->activate = 0;

        $restaurant->save();
        Session::flash('flash_message', 'Restaurante desactivado');

        return Redirect::back();
    }

    public function activate($id) {

        $restaurant = Restaurant::find($id);

        $restaurant->activate = 1;
        $restaurant->save();
        Session::flash('flash_message', 'Restaurante ha sido activado');

        return Redirect::back();
    }

    /**
     * Show the shedule of  specified restaurant.
     * GET /{restslug}/shedules
     *
     * @param  string $restslug
     * @return Response
     */
    public function showschedule($restslug) {
        $restaurant = Restaurant::where('slug', $restslug)->firstOrFail();
        $parent_restaurant = Restaurant::where('restaurant_id', $restaurant->parent_restaurant_id)->firstOrFail();

        $data['message'] = 'Formulario para la edición de horarios de restaurante ' . $restaurant->name;
        $data['error'] = false;
        $data['schedule'] = $parent_restaurant->schedules()->get();
        return $data;
    }

    /**
     * Cretate shedule of  specified restaurant.
     * POST /{restslug}/shedules
     *
     * @param  string $restslug
     * @return Response
     */
    public function createschedule($restslug) {
        $restaurant = Restaurant::where('slug', $restslug)->firstOrFail();
        $validacion = Schedule::validacion(Input::all());

        if ($validacion->fails()) {

            return $validacion->messages();
        } else {

            $existencia = $restaurant->schedules()->where('day_id', Input::get('day_id'))->where('service_type_id', Input::get('service_type_id'))->count();

            if ($existencia == 1) {
                $data['message'] = 'Tipo de horario ya existente';
                $data['error'] = true;

                return $data;
            } else {

                $schedule = new Schedule;
                $schedule->restaurant_id = $restaurant->restaurant_id;
                $schedule->day_id = Input::get('day_id');
                $schedule->opening_time = Input::get('opening_time');
                $schedule->closing_time = Input::get('closing_time');
                $schedule->service_type_id = Input::get('service_type_id');

                $schedule->save();
                return $schedule;
            }
        }
    }

    /**
     * Cretate shedule of  specified restaurant.
     * POST /{restslug}/shedules/update/{id}
     *
     * @param  string $restslug
     * @return Response
     */
    public function storeschedule($restslug, $id) {
        $restaurant = Restaurant::where('slug', $restslug)->firstOrFail();
        $schedule = Schedule::find($id);
        $schedule->opening_time = Input::get('opening_time');
        $schedule->closing_time = Input::get('closing_time');
        $schedule->save();
        return $schedule;
    }

    /**
     * Cretate shedule of  specified restaurant.
     * GET /{restslug}/shedules/update/{id}
     *
     * @param  string $restslug
     * @return Response
     */
    public function updateschedule($restslug, $id) {
        $restaurant = Restaurant::where('slug', $restslug)->firstOrFail();
        $schedule = $restaurant->schedules()->where('schedule_id', $id)->get();
        $data['message'] = 'Formulario para update de horario';
        $data['error'] = false;
        $data['schedule'] = $schedule;
        return $data;
    }

    /**
     * Create shedule of  specified restaurant.
     * POST /{restslug}/shedules/delete/{id}
     *
     * @param  string $restslug
     * @return Response
     */
    public function deleteschedule($restslug, $id) {
        $restaurant = Restaurant::where('slug', $restslug)->firstOrFail();
        Schedule::destroy($id);
        $data['message'] = 'Operación completada con éxito';
        $data['error'] = false;
        return $data;
    }

    /**
     * Add paymenth method.
     * POST /{restslug}/payment-method
     *
     * @param  string $restslug
     * o@return Response
     */
    public function paymentmethod($restslug) {

        $restaurant = ParentRestaurant::getParent($restslug);
        return $restaurant->paymentmethods()->get();
    }

    /**
     * Add paymenth method.
     * GET /{restslug}/about
     *
     * @param  string $restslug
     * o@return Response
     */
    public function about($restslug) {

        $data['restaurant_info'] = ParentRestaurant::getParent($restslug);
        $data['landingpage'] = $data['restaurant_info']->landingpage()->get();
        
        $data['sections'] = $data['restaurant_info']->sections()->where('activate', true)->orderBy('section_order_id', 'asc')->get();
        
        $schedules = $data['restaurant_info']->schedules()->get();
        $data['schedules'] = array();
        
        foreach ($schedules as $schedule) {
            $data['schedules'][$schedule->day_id][$schedule->service_type_id]=array('opening'=> $schedule->opening_time, 'closing'=> $schedule->closing_time);
        }
        
        $data['service_types'] = $data['restaurant_info']->services_types()->get();
        $data['arr_dias'] = $this->arrDias();
        
        $data['sucursales'] = Restaurant::where('parent_restaurant_id', '=', $data['restaurant_info']->restaurant_id)->get();
        
        return View::make('web.restaurant_info', $data);
    }

    /**
     * Display a listing of the resource by promos.
     * GET /{restslug}/promociones
     *
     * @return Response
     */
    public function promos($restslug) {
        // Show  promos by restaurant
        $response['restaurant'] = Restaurant::where('slug', $restslug)->firstOrFail();
        $response['landingpage'] = $response['restaurant']->landingPage;
        $response['products'] = Restaurant::find($response['restaurant']->restaurant_id)
                        ->products()->where('activate', true)
                        ->where('promotion', true)->get();

        return View::make('web.promo_products', $response);
    }

}
