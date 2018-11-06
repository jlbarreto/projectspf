<?php

class UserController extends \BaseController {

	/**
	* Display a listing of the resource.
	*
	* @return Response
	*/
	public function index()
	{
		//
	}


	/**
	* Show the form for creating a new resource.
	*
	* @return Response
	*/
	public function create()
	{
		//
	}


	/**
	* Store a newly created resource in storage.
	*
	* @return Response
	*/
	public function store()
	{
		$input = Input::all();
		try {
			$statusCode = 200;
			/**
			* validate the info, create rules for the inputs
			*
			*/
			$rules = array(
				'email' 			=> 'required|email|unique:com_users',
				'password' 			=> 'required|alphaNum|min:3',
				'terms_acceptance' 	=> 'accepted'
			);

			$display = array(
				'password' 			=> 'contraseña',
				'terms_acceptance' 	=> 'terminos y condiciones'
			);

			//Rules before defined
			$validator = Validator::make($input, $rules);
			//Field name translation
			$validator->setAttributeNames($display);

			if ($validator->fails()) {
				//error
				$response['status'] = false;
				$response['data']  = $validator->messages();
			}else{
				$password = $input['password'];
				if(strlen($password) < 32){
					$password = md5($password);
				}
				//save
				$user = new User;
				#$user->name 			= Input::get('name');
				$user->email 			= $input['email'];
				$user->password 		= Hash::make($password);
				$user->country_id 		= 69; // El Salvador
				$user->status 			= 1; // Activo - Dev version
				$user->terms_acceptance = $input['terms_acceptance'];
				$user->save();

				$response['status'] = true;
				$response['data']  = $user;
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
	* Display the specified resource.
	*
	* @param  int  $id
	* @return Response
	*/
	public function show()
	{
		$input = Input::all();

		// Show User Profile
		try {
			$statusCode = 200;
			$user = User::find($input['user_id']);
			if (!empty($user)) {
				$response['status'] = true;
				$response['data'] = $user;
			} else {
				$response['status'] = false;
				$response['data'] = "User not found.";
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
	*
	* @param  int  $id
	* @return Response
	*/
	public function edit($id)
	{
		//
	}


	/**
	* Update the specified resource in storage.
	*
	* @param  int  $id
	* @return Response
	*/
	public function update()
	{
		$input = Input::all();
		try {
			$statusCode = 200;
			/**
			* validate the info, create rules for the inputs
			*
			*/
			$rules = array(
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
			$validator = Validator::make($input, $rules);
			//Field name translation
			$validator->setAttributeNames($display);

			if ($validator->fails()) {
				//error
				$response['status'] = false;
				$response['data']  = $validator->messages();
			}else{
				//update
				$user = User::find($input['user_id']);
				$user->name 			= $input['name'];
				$user->last_name		= $input['last_name'];
				if(isset($input['gender'])) $user->gender = $input['gender'];
				if(isset($input['birth_date'])) $user->birth_date = $input['birth_date'];
				$user->phone 			= $input['phone'];
				$user->save();

				$response['status'] = true;
				$response['data']  = $user;
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
	* Remove the specified resource from storage.
	*
	* @param  int  $id
	* @return Response
	*/
	public function destroy($id)
	{
		//
	}

	/**
	* Log in.
	*
	* Start session.
	*
	* @return json Status and user data.
	*/
	public function doLogin()
	{
		$input = Input::all();
		try {
			$statusCode = 200;
			// validate the info, create rules for the inputs
			$rules = array(
				'email'    => 'required|email',
				'password' => 'required|alphaNum|min:4'
			);

			// run the validation rules on the inputs from the form
			$validator = Validator::make($input, $rules);

			if ($validator->fails()) {
				//error
				$response['status'] = false;
				$response['data']  = $validator->messages();
			} else {

				$password = $input['password'];
				if(strlen($password) < 32){
					$password = md5($password);
				}

				// create our user data for the authentication
				$userdata = array(
					'email' 	=> $input['email'],
					'password' 	=> $password,
					'status'	=> 1
				);

				// attempt to do the login
				if (Auth::attempt($userdata)) {
					// validation successful!
					// redirect them to the secure section or whatever
					// return Redirect::to('secure');
					// for now we'll just echo success (even though echoing in a controller is bad)
					$response['status'] = true;
					$response['data']  = Auth::user();
				} else {
					// validation not successful, send back to form
					$response['status'] = false;
					$response['data']  = "Tu usuario y clave no coinciden. Vuelve a intentarlo.";
				}

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
	* Log out.
	*
	* Close session.
	*
	* @return json Status and message.
	*/
	public function doLogout() {
		Auth::logout();
		$response = array('status' => true, 'data' => 'Logout success!');

		return Response::json($response, 200);
	}
	/*
	public function fbLogin() {

	$facebook = new Facebook(Config::get('facebook'));
	$params = array(
	'redirect_uri' => url('/login/fb/callback'),
	'scope' => 'email',
);
return Redirect::away($facebook->getLoginUrl($params));
}

public function fbCallback() {
$code = Input::get('code');
if (strlen($code) == 0) return Redirect::to('/')->with('message', 'There was an error communicating with Facebook');

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
*/
/**
* Validate email.
*
* Validate email in DB.
*
* @return json Status and email.
*/
public function emailCheck()
{
	$input = Input::all();
	try {
		$statusCode = 200;
		$response = array();
		$user = User::where('email', '=', $input['email'])->first();
		if(empty($user->user_id)){
			$response['status'] = false;
			$response['data'] = array('message' => 'No existe el email');
		}else{
			$response['status'] = true;
			$response['data'] = array(
				'user_id' => $user->user_id,
				'email' => $user->email,
				'sospecha' => $user->sospecha
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
* Create profile.
*
* Get info from social networks apis.
*
* @return json Status and user id.
*/
public function socialConnect()
{
	$input = Input::all();
	try {
		$statusCode = 200;
		$uid = $input['uid'];
		$profile = Profile::where('uid', '=', $uid)->first();
		if (empty($profile))
		{
			$email = $input['email'];
			$user = User::where('email', '=', $email)->first();
			if(empty($user))
			{
				$user = new User;
				$user->name 	  		= $input['first_name'];
				$user->last_name  		= $input['last_name'];
				$user->email 	  		= $email;
				$user->status 			= 1;
				$user->terms_acceptance = 1;
				$user->photo 	  		= $input['image_url'];
				$user->country_id 		= 69; // El Salvador
				$user->save();
			}

			$user->photo = $input['image_url'];
			$user->save();

			$profile = new Profile();
			$profile->user_id = $user->user_id;
			$profile->uid = $uid;
			$profile = $user->profiles()->save($profile);
		}
		$response['status'] = true;
		$response['data']['user_id'] = $profile->user->user_id;
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
