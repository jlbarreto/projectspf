<?php

class ParentRestaurant{

   	public static function getParent($restslug){

	   	$restaurant = Restaurant::where('slug','=',$restslug)->firstOrFail();
		$parent_restaurant = Restaurant::find($restaurant->parent_restaurant_id);
		return $parent_restaurant;

   	}

   	public static function permission($restslug){
   		$restaurant = Restaurant::where('slug',$restslug)->findOrFail();

			if ($restaurant->restaurant_id == $restaurant->parent_restaurant_id) {
				return true;

			}else{
				return false;

			}
   	}
   	public static function restricciones(){

   		$restricciones = '^((?!order|conditions|payment-method|profile|login|logout|register|cart|promociones|user|restaurant-orders|sections|explorar|tags|search|password|admin|delivery_pidafacil).)*';
         

   		return $restricciones;
   	}

}