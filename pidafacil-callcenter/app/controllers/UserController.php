<?php

class UserController extends BaseController {

	public function __construct(EmailController $emails){
        $this->email = $emails;
    }
	
	public function store() {
        // validate and register
        /**
         * validate the info, create rules for the inputs
         *
         */
        
        $password = Input::get('password');
        if (strlen($password) < 32) {
            $password = md5($password);
        }
        //save
        $user = new User;
        $user->name = Input::get('name');
        $user->last_name = Input::get('last_name');
        $user->email = Input::get('email');
        $user->password = Hash::make($password);
        $user->phone = Input::get('phone');
        $user->country_id = 69; // El Salvador
        $user->status = 1; // Activo - Dev version
        //$user->terms_acceptance = Input::get('terms_acceptance');
        $user->save();

        //Auth::login($user);

        if (Auth::check()) {
            //Guardando el usuario para registrarlo en appboy en la primera vista que cargue
            Session::flash('email', $user->email);
            Session::put('usuario_id', $user->user_id);
            
            $to = Input::get('email');
            $this->email->welcome($to, $user->name . ' ' . $user->last_name);
            
            return Response::json($user);
            
        } else {
        	$user = 0;
            return Response::json($user);
        }
        
    }

	public function show(){
		// Show User Profile
		/*$user = User::find(Input::get('id'));

		$response['status'] = true;
		$response['data'] = $user;
		*/
		return View::make('web.profile')->with('user', Auth::user());
	}

	public function edit($id){ 
		// load the profile form
	}

	public function update(){
		// validate and register
		/**
	     * validate the info, create rules for the inputs
	     *
	     */
	    $rules = array(
			#'email' 			=> 'required|email|unique:com_users',
			'name' 		=> 'required',
			'last_name' => 'required',
			'phone' 	=> 'required'
		);

	    $display = array(
			'name' 		=> 'nombre',
			'last_name' => 'apellido',
			'phone'		=> 'teléfono'
		);

	   	//Rules before defined
		$validator = Validator::make(Input::all(), $rules);
		//Field name translation
		$validator->setAttributeNames($display);
		
		if($validator->fails()){
			//error
			$response['status'] = false;
			$response['data']  = $validator->messages();
		}else{
			$password = Input::get('password');
			if(strlen($password) < 32){
				$password = md5($password);
			}
			//update
			$user = User::find(Input::get('user_id'));
            #$user->email 			= Input::get('email');
            #$user->password 		= Hash::make($password);
            $user->name 			= Input::get('name');
            $user->last_name		= Input::get('last_name');
            $user->gender 			= Input::get('gender');
            $user->birth_date 		= Input::get('birth_date');
            $user->phone 			= Input::get('phone');
            $user->save();

            $response['status'] = true;
            $response['data']  = $user;
		}

		return $response;
	}

	public function showLogin(){
		// load the login form
		return View::make('web.log');
	}
    public function showLoginVisor(){
        // load the login form
        return View::make('web.log_visor');
    }
	public function doLogin(){
		// validate the info, create rules for the inputs
		$rules = array(
			'email'    => 'required|email',
			'password' => 'required|alphaNum|min:4'
		);

		// run the validation rules on the inputs from the form
		$validator = Validator::make(Input::all(), $rules);

		if($validator->fails()){
			//error
            return Redirect::to('/')
                ->withErrors($validator);
		}else{

			$password = Input::get('password');
			if(strlen($password) < 32){
				$password = md5($password);
			}

			// create our user data for the authentication
			$userdata = array(
				'email' 	=> Input::get('email'),
				'password' 	=> $password,
				'status'	=> 1
			);

			// attempt to do the login
			if(Auth::attempt($userdata, Input::get('stay-logged'))){
                // validation successful!
                // redirect them to the secure section or whatever
                // return Redirect::to('secure');
                // for now we'll just echo success (even though echoing in a controller is bad)
                /*
                 *
                 * Se toma el ID del role y se agrega en el objeto Authc:user
                 * para utilizarlo de manera global
                 *
                 * */
                $role = DB::table('assigned_roles')
                    ->select('role_id')
                    ->where('user_id', Auth::user()->user_id)
                    ->get();

                if (isset($role[0])) {
                    Auth::user()['role_id'] = $role[0]->role_id;
                } else {
                    Auth::user()['role_id'] = 0;
                }

                $url = Session::get('pre_login_url');
                $response['status'] = true;
                $response['data'] = Auth::user();
                // If user attempted to access specific URL before logging in


                if(isset($role[0])){
                    if($role[0]->role_id == '1' || $role[0]->role_id == '2' || $role[0]->role_id == '3') {
                        $primerRest = Restaurant::select('restaurant_id')
                            ->orderBy('restaurant_id')
                            ->limit(1)
                            ->get();
                        Session::put('flash_message', 'Usuario Incorrecto');
                        return Redirect::to('/logout');
                    }elseif ($role[0]->role_id == '7'){
                    	/*Código para insertar registros de motoristas estado 15*/
                    	$date = new DateTime();
						$fecha = $date->format('Y-m-d');
                    	$datoOrden = Order::firstOrFail();
                    	$hora = date("H");
                    	//$motoE = MensajeroStatusLog::where('created_at', 'LIKE','%2016-04-09%')->firstOrFail();
                    	$motoE = DB::table('mensajero_status_log')->where('created_at', 'LIKE','%'.$fecha.'%')->first();
                    	
                    	if(isset($motoE) && $motoE != ''){
                    		Log::info('Ya hay registros');
                    	}else{
                    		//if($hora == '09' || $hora == '10' || $hora == '11'){
                    		for($i=1; $i < 11; $i++){ 
	                    		$est15 = new MensajeroStatusLog;
						        $est15->mensajero_status_id = 15;
						        $est15->mensajero_coordenadas = '13.7058893,-89.2448521';
						        $est15->mensajero_comment = 'Ingreso desde call center 10AM';
						        $est15->motorista_id = $i;
						        $est15->order_id = $datoOrden['order_id'];
						        $est15->restaurant_id = $datoOrden['restaurant_id'];
						        $est15->save();
	                    	}
	                    	//}	
                    	}
                    	
                        return Redirect::to('/delivery_pidafacil');
                    }elseif($role[0]->role_id >= '5'){
                        Session::put('flash_message', 'No cuenta con permiso para acceder a esta sección');
                        return Redirect::to('/logout');
                    }else {
                        if (Session::has('pre_login_url')){
                            Session::forget('pre_login_url');
                            return Redirect::to($url);
                        }else{
                            return Redirect::to($url);
                        }
                    }
                }else{
                    Session::put('flash_message', 'No cuenta con permiso para acceder a esta sección');
                    return Redirect::to('/');
                }
			}else{
				// validation not successful, send back to form
                Session::put('flash_message', 'El usuario o la contraseña son incorrectos');
			}
		}
        return Redirect::to('/');
	}

	public function doLogout(){
		Auth::logout();
		//$response = array('status' => true, 'data' => 'Logout success!');
		return Redirect::to('/');
	}

	public function emailCheck(){
		$response = array();
		$user = User::where('email', '=', Input::get('email'))->first();
		if(empty($user->user_id)){
			$response['status'] = false;
			$response['data'] = array('message' => 'No existe el email');
		}else{
			$response['status'] = true;
			$response['data'] = array(
				'user_id' => $user->user_id, 
				'email' => $user->email
			);
		}
		return $response;
	}

	public function MostrarMapa(){
		return View::make('web.mapa');
	}
}