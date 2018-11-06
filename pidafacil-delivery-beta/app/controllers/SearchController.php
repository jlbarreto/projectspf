<?php

class SearchController extends BaseController {

	public function promos($tag = null)
	{
		
		$tags = Tag::whereIn('tag_type_id',array(1, 2))->get();
		if (!empty($tag)) {
			$obj_tag = Tag::where('tag_name', $tag)->firstOrFail();
			$restaurants = Tag::find($obj_tag->tag_id)->restaurants()
				->whereRaw('parent_restaurant_id = res_restaurants.restaurant_id')
				->with('landingPage')->whereHas('products', function($q)
				{
			    	$q->where('promotion', true)->orderBy('res_products.created_at', 'desc');
				})->orderBy('res_restaurants.created_at', 'asc')->get();
		} else {
			$obj_tag = null;
			$restaurants = Restaurant::with('landingPage')
				->whereHas('products', function($q)
				{
			    	$q->where('promotion', true)->orderBy('res_products.created_at', 'desc');
				})->orderBy('res_restaurants.created_at', 'asc')->get();
		}
		return View::make('web.promo')
			->with('tags', $tags)
			->with('tag', $obj_tag)
			->with('restaurants', $restaurants);
	}


	public function explorar($tag = null)
	{
		if (!empty($tag)) {
			//return Response::json(array($tag), 200);
			$obj_tag = Tag::where('tag_name', $tag)->firstOrFail();
			$restaurants = Tag::find($obj_tag->tag_id)->restaurants()
				->whereRaw('parent_restaurant_id = res_restaurants.restaurant_id')
				->with('landingPage')->get();

			$tags = Tag::whereIn('tag_type_id',array(1, 2))->get();

			return View::make('web.explorar')
				->with('restaurants', $restaurants)
				->with('tag', $obj_tag)
				->with('tags', $tags);
		} else {
			$ftypes = Tag::where('tag_type_id',1)->get();
			$fmoods = Tag::where('tag_type_id',2)->get();
			return View::make('web.explorar')
				->with('ftypes', $ftypes)
				->with('fmoods', $fmoods);
		}
	}

	public function autocomplete(){		


		//Si tiene como valor en type : tag_id es un tag si tiene un slug es un restaurante esto es para saber si redireccionar
		//hacia un restaurante o hacia un tag en específico

		$data = array();
		$tags = DB::select('select tags.tag_id, tags.tag_name, tipos_tags.tag_type_id from pidafacil.com_tags as tags inner join pidafacil.com_tag_types as tipos_tags
				on tags.tag_type_id  = tipos_tags.tag_type_id
				order by tipos_tags.tag_type_id ');
		$restaurants = DB::select('select restaurante.restaurant_id, restaurante.name,
						restaurante.slug  from pidafacil.res_restaurants restaurante');


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
		return View::make('autocomplete.autocomple',$data);

		
	}
	public function tags($tag){
		$restaurants = DB::select('select restaurants.name , restaurants.address, restaurants.phone, restaurants.slug, tags.tag_name from pidafacil.res_tags restags 
						inner join pidafacil.res_restaurants restaurants
						on restags.restaurant_id = restaurants.restaurant_id
						inner join pidafacil.com_tags  tags on restags.tag_id = tags.tag_id
						where tags.tag_id = ?', array($tag));
		return $restaurants;
	}

	public function search(){
		$search = Input::get('search');
		$pago = Input::get('payment');
		$service = Input::get('service');
		$query = DB::table('res_restaurants')
				->join('res_tags','res_tags.restaurant_id','=','res_restaurants.restaurant_id')
				->join('com_tags','com_tags.tag_id', '=','res_tags.tag_id');

		if(isset($search)){
			$query = $query->where('res_restaurants.name','LIKE',"%$search%")
						   ->orWhere('com_tags.tag_name','LIKE',"%$search%");
			if(isset($pago)){
				$query = $query->join('res_restaurants_payment_methods','res_restaurants_payment_methods.restaurant_id','=','res_restaurants.restaurant_id')
						 ->join('res_payment_methods','res_payment_methods.payment_method_id','=','res_restaurants_payment_methods.payment_method_id')
						 ->where('res_restaurants_payment_methods.payment_method_id','=',$pago);
			}
			if(isset($service)){
				$query = $query->join('res_restaurants_service_types','res_restaurants_service_types.restaurant_id','=','res_restaurants.restaurant_id')
						->join('res_service_types','res_service_types.service_type_id','=','res_restaurants_service_types.service_type_id')
						->where('res_restaurants_service_types.service_type_id','=',$service);
			}
			
		}else{
			$query = DB::table('res_restaurants');
		}
		return $query->get();
	}

}