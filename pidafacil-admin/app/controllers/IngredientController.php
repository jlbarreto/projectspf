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
     * Show the form for creating a new resource.
     * GET {$restslug}/ingredient/create
     *
     * @return Response
     */
    public function create($restslug) {
        $data['restaurant'] = Restaurant::where('slug', $restslug)->firstOrFail();
        $data['ingredients'] = $data['restaurant']->ingredients()->get();

        return View::make('admin.ingredient_create')->with($data);
    }
    
    public function restaurantList($restslug) {
        $data['restaurant'] = Restaurant::where('slug', $restslug)->firstOrFail();
        $data['ingredients'] = $data['restaurant']->ingredients()->get();
        
        return View::make('admin.ingredient_list')->with($data);
    }

    /**
     * Store a newly created resource in storage.
     * POST {$restslug}/ingredient/create
     *
     * @param string $restslug
     * @return Response
     */
    public function store() {
        $validation = Ingredient::validacion(Input::all());
        
        if ($validation->fails()) {
            $response['status'] = false;
            $response['data']  = $validation->messages();
        } else {
            $count = Ingredient::where('ingredient', Input::get('ingredient'))->where('restaurant_id', Input::get('restaurant_id'))->count();
            if ($count !== 0) {
                $response['status'] = false;
                $response['data']  = array('Error', "Ya existe otro ingrediente con este nombre en el restaurante");
            } else {
                $ingredient = new Ingredient;
                $ingredient->restaurant_id = Input::get('restaurant_id');
                $ingredient->ingredient = Input::get('ingredient');
                $ingredient->save();
                
                $response['status'] = true;
                $response['data']  = "Ingrediente almacenado con éxito";
                $response['id'] = $ingredient->ingredient_id;
                $response['nombre'] = $ingredient->ingredient;
            }
        }
        
        return $response;
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

    /**
     * Show the form for editing the specified resource.
     * GET {$restslug}/ingredient/{id}/edit
     *
     * @param string $restslug
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        $ingredient = Ingredient::find($id);
        $data['ingredient'] = $ingredient;
        $data['restaurant'] = $data['ingredient']->restaurant()->firstOrFail();
        $data['ingredients'] = $data['restaurant']->ingredients()->get();

        return View::make('admin.ingredient_edit')->with($data);
    }

    /**
     * Update the specified resource in storage.
     * POST {$restslug}/ingredient/{id}
     *
     * @param string $restslug
     * @param  int  $id
     * @return Response
     */
    public function update($id) {
        $validation = Ingredient::validacion(Input::all());
        if ($validation->fails()) {
            $response['status'] = false;
            $response['data']  = $validation->messages();
        } else {

            $count = Ingredient::where('ingredient_id', '!=', $id)->where('ingredient', Input::get('ingredient'))->where('restaurant_id', Input::get('restaurant_id'))->count();
            if ($count !== 0) {
                $response['status'] = false;
                $response['data']  = array('Error', "Ya existe otro ingrediente con este nombre en el restaurante");
            } else {
                $ingredient = Ingredient::find($id);
                $ingredient->ingredient = Input::get('ingredient');
                $ingredient->save();
                
                
                $response['status'] = true;
                $response['data']  = 'Ingrediente actualizado con éxito';
                $response['id'] = $id;
                $response['nombre'] = $ingredient->ingredient;
                
            }
        }
        
        return $response;
    }

    /**
     * Remove the specified resource from storage.
     * POST {$restslug}/ingredient/{id}/delete
     *
     * @param  string $restslug
     * @param  int  $id
     * @return Response
     */
    public function destroy($restslug, $id) {
        
    }

    
    public function activateToggle($id) {
        $ingredient = Ingredient::findOrFail($id);
        
        $ingredient->active = ($ingredient->active==1)? 0:1;
        
        $ingredient->save();
        
        $data['ingredient'] = $ingredient;
        $data['error'] = false;

        return $data;
    }
}
