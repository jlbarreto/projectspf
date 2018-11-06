<?php

use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookSession;

class UserController extends BaseController {

    public function __construct(EmailController $emails) {
        $this->email = $emails;
    }

    public function create() {
        // load the register form
    }

    public function store() {
        // validate and register
        /**
         * validate the info, create rules for the inputs
         *
         */
        $rules = array(
            'name' => 'required',
            'email' => 'required|email|unique:com_users',
            'password' => 'required|alphaNum|min:6',
            'password_confirm' => 'required|min:6|same:password',
            #'phone'				=> 'min:8',
            #'country_id'		=> 'required|integer',
            'terms_acceptance' => 'accepted'
        );

        $display = array(
            'name' => 'nombre',
            'password' => 'contraseña',
            'password_confirm' => 'confirmar contraseña',
            #'phone' 			=> 'Tel&eacute;fono',
            #'country_id' 		=> 'país',
            'terms_acceptance' => 'terminos y condiciones'
        );

        //Rules before defined
        $validator = Validator::make(Input::all(), $rules);
        //Field name translation
        $validator->setAttributeNames($display);

        if ($validator->fails()) {
            return Redirect::to('login')
                            ->withErrors($validator)
                            ->withInput(Input::except('password', 'password_confirm'));
        } else {
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
            $user->terms_acceptance = Input::get('terms_acceptance');
            $user->save();

            Auth::login($user);

            if (Auth::check()) {
                $to = Input::get('email');
                $this->email->welcome($to, $user->name . ' ' . $user->last_name);
                return Redirect::to('profile');
            } else {
                return Redirect::to('login');
            }
        }

        return Redirect::to('login');
    }

    public function show() {
        // Show User Profile
        /* $user = User::find(Input::get('id'));

          $response['status'] = true;
          $response['data'] = $user;
         */
        return View::make('web.profile')->with('user', Auth::user());
    }

    public function edit($id) {
        // load the profile form
    }

    public function update() {
        // validate and register
        /**
         * validate the info, create rules for the inputs
         *
         */
        $rules = array(
            #'email' 			=> 'required|email|unique:com_users',
            'name' => 'required',
            'last_name' => 'required',
            'phone' => 'required'
        );

        $display = array(
            'name' => 'nombre',
            'last_name' => 'apellido',
            'phone' => 'teléfono'
        );

        //Rules before defined
        $validator = Validator::make(Input::all(), $rules);
        //Field name translation
        $validator->setAttributeNames($display);

        if ($validator->fails()) {
            //error
            $response['status'] = false;
            $response['data'] = $validator->messages();
        } else {
            $password = Input::get('password');
            if (strlen($password) < 32) {
                $password = md5($password);
            }
            //update
            $user = User::find(Input::get('user_id'));
            #$user->email 			= Input::get('email');
            #$user->password 		= Hash::make($password);
            $user->name = Input::get('name');
            $user->last_name = Input::get('last_name');
            $user->gender = Input::get('gender');
            $user->birth_date = Input::get('birth_date');
            $user->phone = Input::get('phone');
            $user->save();

            $response['status'] = true;
            $response['data'] = $user;
        }

        return $response;
    }

    public function showLogin() {
        Auth::logout();
        // load the login form
        return View::make('web.log');
    }

    public function doLogin() {
        // validate the info, create rules for the inputs
        $rules = array(
            'email' => 'required|email',
            'password' => 'required'
        );

        // run the validation rules on the inputs from the form
        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            //error
            return Redirect::back()->withInput()->withErrors($validator->messages());
        } else {

            $password = Input::get('password');
            if (strlen($password) < 32) {
                $password = md5($password);
            }

            // create our user data for the authentication
            $userdata = array(
                'email' => Input::get('email'),
                'password' => $password,
                'status' => 1
            );

            // attempt to do the login
            if (Auth::attempt($userdata)) {
                // validation successful!
                // redirect them to the secure section or whatever
                // return Redirect::to('secure');
                // for now we'll just echo success (even though echoing in a controller is bad)
                $response['status'] = true;
                $user = Auth::user();
                $response['data'] = $user;

                //If usser is admin go to admin
                if ($user->hasRole("Admin")) {
                    // If user attempted to access specific URL before logging in
                    if (Session::has('pre_login_url')) {
                        $url = Session::get('pre_login_url');
                        Session::forget('pre_login_url');
                        return Redirect::to($url);
                    }else{
                        return Redirect::to("admin");
                    }
                } else {
                    Auth::logout();
                    Session::flash('flash_message', 'Usted no tiene permisos para ingresar a esta sección.');
                }
            } else {
                // validation not successful, send back to form	
                Session::flash('flash_message', 'Tu usuario y clave no coinciden. Vuelve a intentarlo.');
            }
        }

        return Redirect::back()->withInput();
    }

    public function doLogout() {
        Auth::logout();
        //$response = array('status' => true, 'data' => 'Logout success!');

        return Redirect::to('login');
    }

    public function emailCheck() {
        $response = array();
        $user = User::where('email', '=', Input::get('email'))->first();
        if (empty($user->user_id)) {
            $response['status'] = false;
            $response['data'] = array('message' => 'No existe el email');
        } else {
            $response['status'] = true;
            $response['data'] = array(
                'user_id' => $user->user_id,
                'email' => $user->email
            );
        }
        return $response;
    }

}
