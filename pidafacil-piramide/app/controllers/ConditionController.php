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

}
