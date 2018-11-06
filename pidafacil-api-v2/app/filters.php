<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

/*Debe quedar asi*/
App::before(function($request){
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-Request-With, X-CSRF-TOKEN');
    header('Access-Control-Allow-Credentials: true');
});

App::after(function($request, $response){
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-Request-With, X-CSRF-TOKEN');
    header('Access-Control-Allow-Credentials: true');
});

/*App::before(function($request){
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, access-control-allow-headers, X-CSRF-TOKEN');
    header('Access-Control-Allow-Credentials: true');
});

App::after(function($request, $response){
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, access-control-allow-headers, X-CSRF-TOKEN');
    header('Access-Control-Allow-Credentials: true');
});*/


/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('cors', function($route, $request, $response){
	$response->header->set('Access-Controll-Allow-Origin: *');
	return $response;
});

Route::filter('auth', function(){
	if (Auth::guest()){
		if (Request::ajax()){
			return Response::make('Unauthorized', 401);
		}
		else{
			return Redirect::guest('login');
		}
	}
});

Route::filter('auth.basic', function(){
	return Auth::basic(); //"username"
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function(){
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

/*Route::filter('csrf', function(){
	if (Session::token() !== Input::get('_token')){
		throw new Illuminate\Session\TokenMismatchException;
	}
});*/

// If the session token is not the same as the the request header X-Csrf-Token, then return a 400 error.
Route::filter('csrf', function($route, $request){
        Log::info('EL TOKEN DE SESION ES: '.Session::token());	
	Log::info('EL TOKEN REQUEST ES: '.$request->header('X-CSRF-TOKEN'));
if(Session::token() != $request->header('X-CSRF-TOKEN') ){
    	return Response::json('CSRF does not match', 400);
	}
});
