<?php

class AdminController extends \BaseController {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        return View::make('admin.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {
        //Nothing
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store() {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id) {
        //
    }

    private function slugify($text) {
        // replace non letter or digits by -
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

        // trim
        $text = trim($text, '-');

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // lowercase
        $text = strtolower($text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

    public function horarioView(){

        $restaurantsAll = Restaurant::where('activate', 1)->get();
        $restaurants = array();
        
        foreach ($restaurantsAll as $r) {
            if($r->restaurant_id==$r->parent_restaurant_id){
                $restaurants[]=$r;
            }
        }
        //Creo un array con las horas
        $horas = array();
        for ($i=10; $i <= 22; $i++){
           array_push($horas, $i);
        }

        return View::make('admin.updateHorario')
            ->with('restaurants', $restaurants)
            ->with('horas', $horas);
    }

    public function save_schedules(){
        if (Input::get('restauranteSel') == "none"){
            return Redirect::back()
                ->withErrors("Debe elegir un restaurante")
                ->withInput();
        }

        if (Input::get('diaSel') == "none"){
            return Redirect::back()
                ->withErrors("Debe elegir un día de la semana")
                ->withInput();
        }

        $restaurant_id = Input::get('restauranteSel');
        $dia = Input::get('diaSel');
        $horaCierre = Input::get('horaCierre');
        $horaApertura = Input::get('horaApertura');
        $cierre = explode(':', $horaCierre);
        $apertura = explode(':', $horaApertura);
       
        //Traeré los horarios del día a guardar
        $horarios_old = Schedule::where('restaurant_id', $restaurant_id)->where('day_id', $dia)->get();
        //Vartiables para guardar los horarios antiguos
        $open_old = '';
        $close_old = '';
        
        foreach($horarios_old as $old){
            $open_old = $old->opening_time;
            $close_old = $old->closing_time;
        }

        $newHorario = new ScheduleOptions;
        $newHorario->restaurant_id = $restaurant_id;
        $newHorario->day_id = $dia;
        $newHorario->opening_time = trim($apertura[0]).':'.trim($apertura[1]).':00';
        $newHorario->closing_time = trim($cierre[0]).':'.trim($cierre[1]).':00';
        $newHorario->user_id = Auth::user()->user_id;
        $newHorario->opening_time_old = $open_old.':00:00';
        $newHorario->closing_time_old = $close_old.':00:00';
        $newHorario->save();

        return Redirect::route('listaHorario');
    }

    public function all_schedules(){

        $horas_array = array();
        for ($i=8; $i <= 22; $i++){
           array_push($horas_array, $i);
        }

        $horarios = DB::table('res_schedules_options')
                    ->join('res_restaurants', 'res_schedules_options.restaurant_id', '=', 'res_restaurants.restaurant_id')
                    ->select('res_schedules_options.schedules_options_id','res_restaurants.name', 'res_schedules_options.restaurant_id', 'res_schedules_options.day_id', 'res_schedules_options.closing_time', 'res_schedules_options.opening_time')
                    ->get();
        
        $contador = count($horarios);
        if($contador > 0){
            $dia = array();
            $result;
            foreach ($horarios as $row) {
                if($row->day_id == 1){
                    $dia = 'Domingo';
                }elseif($row->day_id == 2){
                    $dia = 'Lunes';
                }elseif($row->day_id == 3){
                    $dia = 'Martes';
                }elseif($row->day_id == 4){
                    $dia = 'Miércoles';
                }elseif($row->day_id == 5){
                    $dia = 'Jueves';
                }elseif($row->day_id == 6){
                    $dia = 'Viernes';
                }elseif($row->day_id == 7){
                    $dia = 'Sábado';
                }
                $result[] = array(
                    'schedules_options_id' => $row->schedules_options_id,
                    'restaurant_id' => $row->restaurant_id,
                    'name' => $row->name,
                    'day_id' => $row->day_id,
                    'closing_time' => $row->closing_time,
                    'opening_time' => $row->opening_time,
                    'dia' => $dia
                );
            }

            //Se hace paginación manual
            $pageNumber = Input::get('page', 1);
            $perpage = 10;
            $slice = array_slice($result, $perpage * ($pageNumber - 1), $perpage);
            $result = Paginator::make($slice, count($result), $perpage);
            
            return View::make('admin.list_horarios')->with(['horarios' => $result, 'horas_array' => $horas_array]);
        }else{
            return View::make('admin.list_horarios')->with(['horarios' => $horarios]);
        }
    }

    public function update_schedules(){
        if(Request::ajax()){
            $restaurante = Input::get('restaurante');
            $dia = Input::get('dia');
            $horaC = Input::get('hora_c');
            $horaA = Input::get('hora_a');
            $id = Input::get('registro');
            $cierre = explode(':', $horaC);
            $apertura = explode(':', $horaA);

            $update = ScheduleOptions::find($id);
            $update->restaurant_id = $restaurante;
            $update->day_id = $dia;
            $update->closing_time = trim($cierre[0]).':'.trim($cierre[1]).':00';
            $update->opening_time =trim($apertura[0]).':'.trim($apertura[1]).':00';
            $update->save();

            return Response::json('Registro actualizado');
        }
    }

    public function drop_schedule(){
        if(Request::ajax()){
            $id = Input::get('registro');

            $borrar = ScheduleOptions::find($id);
            $borrar->delete();

            return Response::json('Registro eliminado');
        }
    }
}
