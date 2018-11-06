<?php

class SearchController extends \BaseController {
	/**
	 * Display a listing of the resource.
	 * GET autocomplete
	 *
	 * @return Response
	 */
	public function autocomplete()
	{
		// Show restaurant list by category
		$input = Input::all();
		try {

			// Si tiene como valor en type : 1 es un tag si tiene un 2 es un restaurante
			// esto es para saber si redireccionar
			// hacia un restaurante o hacia un tag en específico

			$data = array();
			$tags = DB::select('select tags.tag_id, tags.tag_name, tipos_tags.tag_type_id from com_tags as tags
				inner join com_tag_types as tipos_tags on tags.tag_type_id = tipos_tags.tag_type_id
				where tipos_tags.tag_type_id in (1, 2, 4)
				order by tipos_tags.tag_type_id');
			$restaurants = DB::select('select restaurante.restaurant_id, restaurante.name, restaurante.slug
				from res_restaurants restaurante
				where restaurante.parent_restaurant_id = restaurante.restaurant_id
				and restaurante.activate = 1');


			foreach($tags as $tag){
				$add = array('label' => $tag->tag_name,
							 'type'  => "1",
							 'value' => $tag->tag_id
							 );

				array_push($data, $add);
			}
			foreach($restaurants as $restaurant){
				$add = array('label' => $restaurant->name,
							 'type'  => "2",
							 'value' => $restaurant->restaurant_id);
				array_push($data, $add);
			}

			$statusCode = 200;
			$response = array(
				"status" => true,
	        	"data" => $data
	        );

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
