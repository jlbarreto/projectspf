<?php

Route::group(array('before' => 'auth', 'prefix' => 'admin'), function() {
    Route::group(array('before' => 'admin_only'), function() {
        Route::get('/', array('uses' => 'AdminController@index'));

        //Para restaurantes
        Route::group(array('prefix' => '/restaurant'), function() {
            Route::get('/list', array('uses' => 'RestaurantController@all'));
            Route::get('/branchs/{restslug}', array('uses' => 'RestaurantController@branchs'));
            Route::get('/create', array('uses' => 'RestaurantController@create'));
            Route::post('/store', array('uses' => 'RestaurantController@store'));
            Route::get('/edit/{restslug}', array('uses' => 'RestaurantController@edit'));
            Route::get('/desactivate/{id}', array('uses' => 'RestaurantController@desactivate'));
            Route::get('/activate/{id}', array('uses' => 'RestaurantController@activate'));
            Route::post('/update/{id}', array('uses' => 'RestaurantController@update'));
            Route::get('/define_horarios/{restslug}', array('uses' => 'RestaurantController@define_horarios'));
            Route::post('/set_horarios/{restslug}', array('uses' => 'RestaurantController@set_horarios'));
            Route::get('/sections/edit/{restslug}', array('uses' => 'SectionController@edit'));
            Route::get('/sections/changeActivate/{id}', array('uses' => 'SectionController@activate'));
            Route::post('/sections/add/{restslug}', array('uses' => 'SectionController@add'));
            Route::post('/sections/positions', array('uses' => 'SectionController@positions'));
            Route::get('/products/{restslug}', array('uses' => 'ProductController@index'));

            //Ruta para vista de horarios restaurantes
            Route::get('/horarios', array('uses' => 'AdminController@horarioView'));

            //Ruta para guardar los nuevos horarios de cada restaurantes
            Route::post('/new_schedules', array('uses' => 'AdminController@save_schedules'));

            //Ruta para listar todos los horarios que se creen
            Route::get('/list_schedules', array('as' => 'listaHorario', 'uses' => 'AdminController@all_schedules'));

            //Ruta para editar un horario
            Route::post('/edit_schedules', array('uses' => 'AdminController@update_schedules'));

            //Ruta para eliminar un horario especifico
            Route::post('/delete_schedule', array('uses' => 'AdminController@drop_schedule'));
        });
        
        //Para productos
        Route::group(array('prefix' => '/product'), function() {
            Route::get('/section/{id}', array('uses' => 'ProductController@sectionList'));
            Route::get('/create/{id_section}', array('uses' => 'ProductController@create'));
            Route::post('/store', array('uses' => 'ProductController@store'));
            Route::get('/edit/{id}', array('uses' => 'ProductController@edit'));
            Route::post('/update/{id}', array('uses' => 'ProductController@update'));
            Route::get('/changeActivate/{id}', array('uses' => 'ProductController@changeActivate'));
        });
        
        //Para ingredientes
        Route::group(array('prefix' => '/ingredient'), function() {
            Route::get('/restaurant/{restslug}', array('uses' => 'IngredientController@restaurantList'));
            Route::get('/create/{restslug}', array('uses' => 'IngredientController@create'));
            Route::post('/store', array('uses' => 'IngredientController@store'));
            Route::get('/edit/{id}', array('uses' => 'IngredientController@edit'));
            Route::post('/update/{id}', array('uses' => 'IngredientController@update'));
            Route::get('/activateToggle/{id}', array('uses' => 'IngredientController@activateToggle'));
        });
        
        //Para condiciones
        Route::group(array('prefix' => '/condition'), function() {
            Route::get('/restaurant/{restslug}', array('uses' => 'ConditionController@index'));
            Route::get('/create/{restslug}', array('uses' => 'ConditionController@create'));
            Route::get('/destroy/{id}', array('uses' => 'ConditionController@destroy'));
            Route::post('/store', array('uses' => 'ConditionController@store'));
            Route::get('/edit/{id}', array('uses' => 'ConditionController@edit'));
            Route::post('/update/{id}', array('uses' => 'ConditionController@update'));
            Route::get('/options/{id}', array('uses' => 'ConditionController@getOptions'));
            Route::get('/activateToggleOption/{id}', array('uses' => 'ConditionController@activateToggleOption'));
            
            //Para las opciones
//            Route::group(array('prefix' => '/option'), function() {
//                Route::post('/update/{id}', array('uses' => 'ConditionController@optionUpdate'));
//                Route::post('/store', array('uses' => 'ConditionController@optionStore'));
//                Route::get('/delete/{id}', array('uses' => 'ConditionController@optionDelete'));
//            });
        });
    });
});


Route::filter('admin_only', function() {
    if (!Auth::user()->hasRole('Admin')) {
        //TODO: Redirect to message no permission!
        return Redirect::to('/');
    }
});
