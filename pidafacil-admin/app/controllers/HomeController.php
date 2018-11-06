<?php

class HomeController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function showWelcome()
	{
		//Si tiene como valor en type : tag_id es un tag si tiene un slug es un restaurante esto es para saber si redireccionar
		//hacia un restaurante o hacia un tag en específico
		/*
		if (Session::has('autocomplete')){
			$data['autos'] = Session::get('autocomplete');
		}else{
			$data = array();
			$tags = DB::select('select tags.tag_id, tags.tag_name, tipos_tags.tag_type_id from pidafacil.com_tags as tags 
				inner join pidafacil.com_tag_types as tipos_tags on tags.tag_type_id = tipos_tags.tag_type_id
				where tipos_tags.tag_type_id in (1, 2)
				order by tipos_tags.tag_type_id');
			$restaurants = DB::select('select restaurante.restaurant_id, restaurante.name, restaurante.slug 
				from pidafacil.res_restaurants restaurante 
				where restaurante.parent_restaurant_id = restaurante.restaurant_id');
			

			foreach($tags as $tag){
				$add = array('label' => $tag->tag_name,
							 'type'  => 'tag_id',
							 'value' => $tag->tag_id
							 );

				array_push($data, $add);		
			}
			foreach($restaurants as $restaurant){
				$add = array('label' => $restaurant->name, 
							 'type'  => 'slug',
							 'value' => $restaurant->slug);
				array_push($data, $add);		
			}
			
			Session::put('autocomplete', $data);
			$data['autos'] = Session::get('autocomplete');
		}
		*/

		return View::make('web.home');
	}

}
