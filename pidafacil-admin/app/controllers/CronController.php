<?php

class CronController extends \BaseController {

	public function verificar(){
		$horarios = ScheduleOptions::join('res_restaurants', 'res_schedules_options.restaurant_id', '=', 'res_restaurants.restaurant_id')
                ->select('res_schedules_options.schedules_options_id','res_restaurants.name', 'res_schedules_options.restaurant_id', 'res_schedules_options.day_id', 'res_schedules_options.closing_time', 'res_schedules_options.opening_time')
                ->get();

        $hora_actual = date("H");
        $dia_actual = date("w");
        $prefijo;
        foreach ($horarios as $row){
        	$prefijo = explode(':', $row->closing_time);
        	Log::info($prefijo[0]);
        	if ($hora_actual == $prefijo[0] && $dia_actual == $row->day_id) {
        		$datos = DB::table('res_schedules')
			            ->where('restaurant_id', $row->restaurant_id)
			            ->where('day_id', $row->day_id)
			            ->update(array('opening_time' => $row->opening_time, 'closing_time' => $row->closing_time));

				Log::info("registro modificado");			            
        	}
        }
	}
}