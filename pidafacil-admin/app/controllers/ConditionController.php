<?php

class ConditionController extends \BaseController {

    /**
     * Display a listing of the resource.
     * GET /condition
     *
     * @return Response
     */
    public function index($restslug) {
        $data['restaurant'] = Restaurant::where('slug', $restslug)->firstOrFail();
        $data['conditions'] = $data['restaurant']->getConditions();

        return View::make('admin.condition_list')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     * GET /condition/create
     *
     * @return Response
     */
    public function create() {
        $data['message'] = 'Formulario para creación de condiciones';
        $data['error'] = false;
        return $data;
    }

    /**
     * Store a newly created resource in storage.
     * POST /condition
     *
     * @return Response
     */
    public function store() {
        $validacion = Condition::validacion(Input::all());
        if ($validacion->fails()) {

            $data['status'] = false;
            $data['data'] = $validacion->messages();
        } else {

            $condition = new Condition();
            $condition->condition = Input::get('condition');
            $condition->save();

            $this->saveSync($condition);

            $data['status'] = true;
            $data['data'] = 'Actualizado con éxito';
            $data['id'] = $condition->condition_id;
            $data['nombre'] = $condition->condition;
        }

        return $data;
    }

    /**
     * Display the specified resource.
     * GET /condition/{id}
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id) {
        $condition = Condition::findOrFail($id);
        return $condition;
    }

    /**
     * Show the form for editing the specified resource.
     * GET /condition/{id}/edit
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        $condition = Condition::findOrFail($id);
        $data['condition'] = $condition;
        $data['message'] = 'Formulario para edición de condicion';
        $data['error'] = false;
        return $data;
    }

    /**
     * Update the specified resource in storage.
     * POST /condition/{id}/edit
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id) {
        $validacion = Condition::validacion(Input::all());
        if ($validacion->fails()) {

            $data['status'] = false;
            $data['data'] = $validacion->messages();
        } else {
            $condition = Condition::findOrFail($id);

            $condition->condition = Input::get('condition');
            $condition->save();

            $this->saveSync($condition);

            $data['status'] = true;
            $data['data'] = 'Actualizado con éxito';
        }

        return $data;
    }

    private function saveSync(Condition $condition) {
        $restaurant = Restaurant::findOrFail(Input::get('restaurant_id'));
        $arrOptions = json_decode(Input::get('options'));
        $actuales = $condition->opciones()->get();

//Eliminando
        foreach ($actuales as $opcion) {
            $exist = false;
            foreach ($arrOptions as $value) {
                if ($value->id == $opcion->condition_option_id) {
                    $exist = true;
                }
            }

            if (!$exist) {
                $restaurant->conditionoptions()->detach($opcion);
                $opcion->delete();
            }
        }

//Actualizando los que vienen del cliente
        foreach ($arrOptions as $v) {
            if ($v->id != "0") {
                $option = ConditionOption::findOrFail($v->id);
            } else {
                $option = new ConditionOption();
            }

            $option->condition_option = $v->nombre;
            $option->condition_id = $condition->condition_id;
            $option->save();

            $restaurant->conditionoptions()->detach($option);
            $restaurant->conditionoptions()->attach($option);
        }
    }

    public function getOptions($id) {
        $condition = Condition::findOrFail($id);

        $options = $condition->opciones()->get();

        $data['options'] = array();

        foreach ($options as $option) {
            $data['options'][] = array('id' => $option->condition_option_id, 
                'nombre' => $option->condition_option, 'active' => $option->active);
        }

        return $data;
    }

    /**
     * Remove the specified resource from storage.
     * POST /condition/{id}/delete
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id) {
        $data['id'] = $id;
        $data['status'] = true;
        
        try {
            Condition::destroy($id);
        } catch (Exception $e) {
            $data['status'] = false;
        }


        return $data;
    }

    /**
     * Show options of the specified resource from storage.
     * GET /condition/options
     *
     * @param  int  $id
     * @return Response
     */
    public function showoption($id) {
        $data['condition'] = Condition::findOrFail($id);
        $data['options'] = Condition::findOrFail($id)->opciones;

        return $data;
    }

    /**
     * Add options of the specified resource from storage.
     * GET /condition/{id}/add
     *
     * @param  int  $id
     * @return Response
     */
    public function addoption($id) {
        $data['condition'] = Condition::findOrFail($id);
        $data['options'] = Condition::findOrFail($id)->opciones;
        $data['message'] = 'Formulario para añadir una opción a la condicion especificada';

        return $data;
    }

    /**
     * Store options of the specified resource from storage.
     * POST /condition/{id}/add
     *
     * @param  int  $id
     * @return Response
     */
    public function storeoption($id) {

        $validacion = ConditionOption::validacion(Input::all());
        if ($validacion->fails()) {

            return $validacion->messages();
        } else {

            $condition = Condition::findOrFail($id);
            $existencia = $condition->opciones()->where('condition_option', Input::get('condition_option'))->count();
            if ($existencia == 0) {
                $conditionOption = new ConditionOption;
                $conditionOption->condition_option = Input::get('condition_option');
                $condition->opciones()->save($conditionOption);
                $data['condition'] = $condition->opciones;
                ;
                $data['error'] = false;
            } else {

                $data['error'] = true;
                $data['message'] = 'Ocurrió un error verifique la existencia';
            }
            return $data;
        }
    }

    /**
     * Delete options of the specified resource from storage.
     * POST /condition/{id}/delete
     *
     * @param  int  $id
     * @return Response
     */
    public function deleteoption($id) {
        $condition = Condition::findOrFail($id);
        $existencia = $condition->opciones()->where('condition_option', Input::get('condition_option'))->count();
        if ($existencia == 0) {

            $data['error'] = true;
            $data['message'] = 'Ocurrió un error verifique la existencia';
        } else {

            $conditionOption = $condition->opciones()->where('condition_option', Input::get('condition_option'))->first();
            $conditionOption->delete();
            $data['error'] = false;
        }
        return $data;
    }

    /**
     * Form to add special options.
     * GET /condition/{id}/add-special
     *
     * @param  int  $id
     * @return Response
     */
    public function addspecialoption($id) {
        $condition = Condition::findOrFail($id);
        $condition->opciones;
        $data['condition'] = $condition;
        $restaurant = Restaurant::all();
        $data['restaurant'] = $restaurant;

        return $data;
    }

    /**
     * Store options of the specified resource from storage.
     * POST /condition/{id}/add-special
     *
     * @param  int  $id
     * @return Response
     */
    public function storespecialoption($id) {
        $restaurant = Restaurant::findOrFail(Input::get('restaurant_id'));
        $condition = Condition::findOrFail($id);
        $existencia = $condition->opciones()->where('condition_option', Input::get('condition_option'))->count();

        if ($existencia == 0) {
            $conditionOption = new ConditionOption;
            $conditionOption->condition_option = Input::get('condition_option');
            $condition->opciones()->save($conditionOption);
            $restaurant->conditionoptions()->attach($conditionOption);
            $data['condition'] = $condition->opciones;
            ;
            $data['error'] = false;
        } else {

            $data['error'] = true;
            $data['message'] = 'Ocurrió un error verifique la existencia';
        }


        return $data;
    }

    /**
     * DELETE options of the specified resource from storage.
     * POST /condition/{id}/delete-special
     *
     * @param  int  $id
     * @return Response
     */
    public function deletespecialoption($id) {
        $restaurant = Restaurant::findOrFail(Input::get('restaurant_id'));
        $condition = Condition::findOrFail($id);
        $existencia = $condition->opciones()->where('condition_option_id', Input::get('condition_option_id'))->count();

        if ($existencia == 0) {
            $data['error'] = true;
            $data['message'] = 'Ocurrió un error verifique la existencia';
        } else {

            $conditionOption = ConditionOption::findOrFail(Input::get('condition_option_id'));
            $restaurant->conditionoptions()->detach($conditionOption);
            $data['condition'] = $condition->opciones;
            $data['error'] = false;
        }

        return $data;
    }
    
    public function activateToggleOption($id) {
        $option = ConditionOption::findOrFail($id);
        
        $option->active = ($option->active==1)? 0:1;
        
        $option->save();
        
        $data['option'] = $option;
        $data['error'] = false;

        return $data;
    }

}
