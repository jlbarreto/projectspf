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

App::before(function($request)
{
	//
});


App::after(function($request, $response)
{
	//
});

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

/*Route::filter('auth', function(){
	if (Auth::guest()){
		// Save the attempted URL
		Session::put('pre_login_url', URL::current());
		if (Request::ajax()) {
			return Response::make('Unauthorized', 401);
		}else{
			return Redirect::guest('restauran_login');
		}
	}
});*/

Route::filter('role_visor', function(){

    $role = DB::table('assigned_roles')
                ->select('role_id')
                ->where('user_id',Auth::user()->user_id)
                ->get();

    if(isset($role[0])){
        Auth::user()['role_id'] = $role[0]->role_id;
    }else{
        Auth::user()['role_id'] = 0;
    }

    // Save the attempted URL
    Session::put('pre_login_url', URL::current());

    //Se divide la url para tomar el id del restaurant donde se quiere acceder.
    $arrurl= explode('/',Session::get('pre_login_url'));
    $conturl = count($arrurl) - 1;
    //se consulta la db para ver a que restaurant tiene asignado el usuario
    $resId = DB::table('res_user')
                ->select('restaurant_id')
                ->where('user_id',Auth::user()->user_id)
                ->get();
    //variable con se utiliza como contador/bandera.
    $con=0;

    //call centre sucursal
    if (Auth::user()->role_id > 0 && Auth::user()->role_id == 2) {
        /*
         * verifica que el usuario tenga permisos para ver la info de este restaurant
         *
         * */

        for($i = 0; $i < count($resId); $i++){
            if($resId[$i]->restaurant_id == $arrurl[$conturl]){
                $con = $resId[$i]->restaurant_id;
            }
        }
        if($con > 0){
            /*
             *
             *si es mayor a cero es porque el usurairo tiene el restaurant
             *asignado, entonces no se hace nada y se deja en la url donde
             *se encuentra
             *
             * */
        }else{
            /*
             * si es igual a cero no tiene permiso par ver ese restaurant
             * se verifica si tiene permiso de ver algun restaurant,
             * si tiene permiso lo redirecciona, si no tiene permiso de ver ningun
             * restauran le envia un mensaje
             *
             * */
            if(isset($resId[0])){
                return Redirect::guest('restaurant-orders/'.$resId[0]->restaurant_id);
            }else{
                return Response::make('Este usuario no tiene asignado ningun restaurant', 401);
            }
        }
        //Role 3, call center restaurante
    }elseif(Auth::user()->role_id > 0 && Auth::user()->role_id == 3){
        for($i = 0; $i < count($resId); $i++)
        {
            $restaurante = DB::table('res_restaurants')
                ->select('restaurant_id')
                ->where('parent_restaurant_id', $resId[$i]->restaurant_id)
                ->get();
            for($j=0; $j < count($restaurante); $j++)
            {
                if($restaurante[$j]->restaurant_id == $arrurl[$conturl])
                {
                    $con++;
                    break;
                }
            }
        }
        if($con > 0)
        {
        }else{
            if(isset($resId[0]))
            {
                return Redirect::guest('restaurant-orders/'.$resId[0]->restaurant_id);
            }else{
                return Response::make('Este usuario no tiene asignado ningun restaurant', 401);
            }
        }
        /*Role 1, call center pidafacil, ve todo */
    }elseif(Auth::user()->role_id > 0 && Auth::user()->role_id == 1){
        
    }else{
        return Redirect::guest('restauran_login');
    }
});





Route::filter('auth.basic', function()
{
	return Auth::basic();
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

Route::filter('guest', function()
{
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

Route::filter('csrf', function()
{
	if (Session::token() != Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});
