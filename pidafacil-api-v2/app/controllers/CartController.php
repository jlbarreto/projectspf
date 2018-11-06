<?php

class CartController extends \BaseController {

	public function checkout(){
		$response = 1;
		return Response::json($response);
	}

}