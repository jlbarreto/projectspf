<?php
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookSession;

class UserController extends BaseController {

	public function __construct(EmailController $emails)
    {
        $this->email = $emails;
    }
	
	public function create() {
		// load the register form
	}

	public function store() 
	{
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

		if ($validator->fails()) {
			return Redirect::to('/restauran_login')
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

            if (Auth::check()) {
            	$to = Input::get('email');
            	$this->email->welcome($to, $user->name .' '. $user->last_name);
            	return Redirect::to('profile');
        	}else{
        		return Redirect::to('login');
        	}
		}

		return Redirect::to('/restauran_login');
	}

	public function show() {
		// Show User Profile
		/*$user = User::find(Input::get('id'));

		$response['status'] = true;
		$response['data'] = $user;
		*/
		return View::make('web.profile')->with('user', Auth::user());
	}

	public function edit($id) { 
		// load the profile form
	}

	public function update() 
	{ 
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
		
		if ($validator->fails()) {
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

	public function showLogin() {
		// load the login form
		return View::make('web.log');
	}
    public function showLoginVisor() {
        // load the login form
        return View::make('web.log_visor');
    }
	public function doLogin() 
	{
		// validate the info, create rules for the inputs
		$rules = array(
			'email'    => 'required|email',
			'password' => 'required|alphaNum|min:4'
		);

		// run the validation rules on the inputs from the form
		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails()) {
			//error
            return Redirect::to('/restauran_login')
                ->withErrors($validator);
		} else {

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
			if (Auth::attempt($userdata, Input::get('stay-logged'))) {
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


                if(isset($role[0]))
                {
                    if ($role[0]->role_id == '1' || $role[0]->role_id == '2' || $role[0]->role_id == '3') {
                        $primerRest = Restaurant::select('restaurant_id')
                            ->orderBy('restaurant_id')
                            ->limit(1)
                            ->get();
                        return Redirect::to('/restaurant-orders/'.$primerRest[0]->restaurant_id);
                    } elseif ($role[0]->role_id == '4') {
                        return Redirect::to('/delivery_pidafacil');
                    } elseif($role[0]->role_id >= '5')
                    {
                        Session::put('flash_message', 'No cuenta con permiso para acceder a esta sección');
                        return Redirect::to('/logout');
                    }
                    else {
                        if (Session::has('pre_login_url')) {

                            Session::forget('pre_login_url');

                            return Redirect::to($url);

                        } else {
                            return Redirect::to($url);

                        }
                    }
                }else{
                    Session::put('flash_message', 'No cuenta con permiso para acceder a esta sección');
                    return Redirect::to('/logout');
                }
			} else {
				// validation not successful, send back to form
                Session::put('flash_message', 'El usuario o la contraseña son incorrectos');
			}

		}
        return Redirect::to('/logout');
	}

	public function doLogout() {
		Auth::logout();
		//$response = array('status' => true, 'data' => 'Logout success!');

		return Redirect::to('/');
	}

	public function fbLogin() {
		$appId  = Config::get('facebook.appId');
		$secret = Config::get('facebook.secret');

		$helper = new FacebookRedirectLoginHelper(url('/login/fb/callback'), $appId, $secret);
		$helper->disableSessionStatusCheck();
		$loginUrl = $helper->getLoginUrl();
		return Redirect::away($loginUrl);
	}

	public function fbCallback() {
		
		$code = Input::get('code');
		$state = Input::get('state');			
		$appId  = Config::get('facebook.appId');
		$secret = Config::get('facebook.secret');

		if (strlen($code) == 0) return Redirect::to('/')->with('message', 'There was an error communicating with Facebook');

		
		$session = new FacebookSession($state.'|'.$code);

		//return Response::json($session->getUserId(), 200);


		$request = new FacebookRequest($session, 'GET', '/me');
		$response = $request->execute();
		$graphObject = $response->getGraphObject();
		$response = $graphObject;

		return Response::json($response, 200);
		
		$facebook = new Facebook(Config::get('facebook'));
		$uid = $facebook->getUser();

		if ($uid == 0) return Redirect::to('/')->with('message', 'There was an error');

		$me = $facebook->api('/me');
		//return Response::json($me);


		$profile = Profile::whereUid($uid)->first();
		if (empty($profile)) {

			//$mepic = $facebook->api('/me/picture?redirect=0&type=large');
			//return Response::json($mepic);

		    $user = User::where('email', '=', $me['email'])->first();
			
			if(empty($user->user_id)){
				$user = new User;
			    $user->name = $me['first_name'];
			    $user->last_name = $me['last_name'];
			    $user->email = $me['email'];
			    $user->photo = 'https://graph.facebook.com/'.$uid.'/picture?type=large';
			    $user->country_id = 69;

			    $user->save();
			}

		    $profile = new Profile();
		    $profile->user_id = $user->user_id;
		    $profile->uid = $uid;
		    //$profile->username = $me['username'];
		    $profile = $user->profiles()->save($profile);
		}

		$profile->access_token = $facebook->getAccessToken();
		$profile->save();

		$user = $profile->user;

		Auth::login($user);

		return Redirect::to('/')->with('message', 'Logged in with Facebook');
	}

	public function emailCheck() 
	{
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

	public function socialConnect()
	{
		$uid = Input::get('uid');
		$profile = Profile::where('uid', '=', $uid)->first();
		if (empty($profile)) 
		{
			//$mepic = $facebook->api('/me/picture?redirect=0&type=large');
			//return Response::json($mepic);
			$email = Input::get('email');
		    $user = User::where('email', '=', $email)->first();
			
			if(empty($user))
			{
				$user = new User;
			    $user->name 	  		= Input::get('first_name');
			    $user->last_name  		= Input::get('last_name');
			    $user->email 	  		= $email;
			    $user->status 			= 1;
			    $user->terms_acceptance = 1;
			    $user->photo 	  		= Input::get('image_url');
			    $user->country_id = 69; // El Salvador
			    $user->save();
			}

		    $profile = new Profile();
		    $profile->user_id = $user->user_id;
		    $profile->uid = $uid;
		    //$profile->username = $me['username'];
		    $profile = $user->profiles()->save($profile);
		}
		//$profile->access_token = Input::get('getAccessToken');
		$profile->save();

		$response['status'] = true;
		$response['data']['user_id'] = $profile->user->user_id;

		return $response;
	}
	public function getGoogleLogin($auth=NULL)
	{
        if ($auth == 'auth')
        {
             Hybrid_Endpoint::process();

        }
        try {
            $oauth = new Hybrid_Auth(app_path() . '/config/google_auth.php');
            $provider = $oauth->authenticate('Google');
            $profileg = $provider->getUserProfile();
            $profile = Profile::whereUid($profileg->identifier)->first();
		if (empty($profile)) 
		{

		    $user = User::where('email', '=', $profileg->email)->first();
			
			if(empty($user->user_id)){
				$user = new User;
			    $user->name = $profileg->firstName;
			    $user->last_name = $profileg->lastName;
			    $user->email = $profileg->email;
			    $user->photo = $profileg->photoURL;
			    $user->country_id = 69;

			    $user->save();
			}

		    $profile = new Profile();
		    $profile->user_id = $user->user_id;
		    $profile->uid = $profileg->identifier;
		    $profile = $user->profiles()->save($profile);
		}

        }
        catch(exception $e)
        {
            return $e->getMessage();
        }
        Auth::loginUsingId($user->user_id);
        return var_dump($profileg);
	}



	    public function getFacebookLogin($auth=NULL)
    {
        if ($auth == 'auth')
        {
            try
            {
                Hybrid_Endpoint::process();
            }
            catch (Exception $e)
            {
                return Redirect::to('fbauth');
            }
            return;
        }

        $oauth = new Hybrid_Auth(app_path(). '/config/facebook.php');
        $provider = $oauth->authenticate('Facebook');
        $profilefb = $provider->getUserProfile();

        $profile = Profile::whereUid($profilefb->identifier)->first();
		if (empty($profile)) {

		    $user = User::where('email', '=', $profilefb->email)->first();
			
			if(empty($user->user_id)){
				$user = new User;
			    $user->name = $profilefb->firstName;
			    $user->last_name = $profilefb->lastName;
			    $user->email = $profilefb->email;
			    $user->photo = $profilefb->photoURL;
			    $user->country_id = 69;

			    $user->save();
			}

		    $profile = new Profile();
		    $profile->user_id = $user->user_id;
		    $profile->uid = $profilefb->identifier;
		    $profile = $user->profiles()->save($profile);
		}
		Auth::loginUsingId($profile->user_id);
        return var_dump($profilefb).'<a href="logout">Log Out</a>';
    }

}