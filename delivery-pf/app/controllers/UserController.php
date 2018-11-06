<?php

class UserController extends BaseController {

	public function __construct(EmailController $emails){
        $this->email = $emails;
    }
	
	public function store(){
		// validate and register
		/**
	     * validate the info, create rules for the inputs
	     *
	     */
	    $rules = array(
			'name' 				=> 'required',
			'email' 			=> 'required|email|unique:com_users',
			'password' 			=> 'required|alphaNum|min:6',
        	'password_confirm' 	=> 'required|min:6|same:password',
			#'phone'				=> 'min:8',
			#'country_id'		=> 'required|integer',
			'terms_acceptance' 	=> 'accepted'
		);

	    $display = array(	    	
			'name' 				=> 'nombre',
			'password' 			=> 'contraseña',
			'password_confirm' 	=> 'confirmar contraseña',
			#'phone' 			=> 'Tel&eacute;fono',
			#'country_id' 		=> 'país',
			'terms_acceptance' 	=> 'terminos y condiciones'
		);

	   	//Rules before defined
		$validator = Validator::make(Input::all(), $rules);
		//Field name translation
		$validator->setAttributeNames($display);

		if($validator->fails()){
			return Redirect::to('/')
            	->withErrors($validator)
            	->withInput(Input::except('password', 'password_confirm'));
		}else{
			$password = Input::get('password');
			if(strlen($password) < 32){
				$password = md5($password);
			}
			//save
			$user = new User;
            $user->name 			= Input::get('name');
            $user->last_name 		= Input::get('last_name');
            $user->email 			= Input::get('email');
            $user->password 		= Hash::make($password);
            $user->phone			= Input::get('phone');
            $user->country_id 		= 69; // El Salvador
            $user->status 			= 1; // Activo - Dev version
            $user->terms_acceptance = Input::get('terms_acceptance');
            $user->save();

			Auth::login($user);

            if (Auth::check()){
            	$to = Input::get('email');
            	$this->email->welcome($to, $user->name .' '. $user->last_name);
            	return Redirect::to('profile');
        	}else{
        		return Redirect::to('login');
        	}
		}

		return Redirect::to('/');
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
                }else{
                    Auth::user()['role_id'] = 0;
                }

                $url = Session::get('pre_login_url');
                $response['status'] = true;
                $response['data'] = Auth::user();
                // If user attempted to access specific URL before logging in


                if(isset($role[0])){
                    if ($role[0]->role_id == '1' || $role[0]->role_id == '2' || $role[0]->role_id == '3') {
                        $primerRest = Restaurant::select('restaurant_id')
                            ->orderBy('restaurant_id')
                            ->limit(1)
                            ->get();
                        return Redirect::to('/');
                    } elseif ($role[0]->role_id == '8'){
                        return Redirect::to('/delivery_pidafacil');
                    }elseif ($role[0]->role_id == '9'){
                    	return Redirect::to('/ventas_rest');                    
                    } elseif($role[0]->role_id >= '5' && $role[0]->role_id <= '8'){
                        Session::put('flash_message', 'No cuenta con permiso para acceder a esta sección');
                        return Redirect::to('/');
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
}