<?php

class IngredientController extends \BaseController {

    /**
     * Display a listing of the resource.
     * GET {$restslug}/ingredient
     *
     * @param string $restslug
     * @return Response
     */
    public function index($restslug) {
        $restaurant = Restaurant::where('slug', $restslug)->with('ingredients')->firstOrFail();
        return $restaurant;
    }

    /**
     * Display the specified resource.
     * GET {$restslug}/ingredient/{id}
     *
     * @param string $restslug
     * @param  int  $id
     * @return Response
     */
    public function show($id) {
        $ingredient = Ingredient::find($id);
        return $ingredient;
    }

}
