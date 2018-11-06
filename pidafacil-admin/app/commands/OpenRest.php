<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class OpenRest extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'open:rest';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(){
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire(){
		//
		$horarios = ScheduleOptions::join('res_restaurants', 'res_schedules_options.restaurant_id', '=', 'res_restaurants.restaurant_id')
                ->select('res_schedules_options.schedules_options_id','res_restaurants.name', 'res_schedules_options.restaurant_id', 'res_schedules_options.day_id', 'res_schedules_options.closing_time', 'res_schedules_options.opening_time', 'res_schedules_options.closing_time_old', 'res_schedules_options.opening_time_old')
                ->get();

        $hora_actual = date("H");
        $dia_actual = date("w") + 1;
        $prefijoA;

        foreach ($horarios as $row){
        	$prefijoA = explode(':', $row->opening_time);

        	if($hora_actual >= $prefijoA[0] && $dia_actual == $row->day_id){
				$datos = DB::table('res_schedules')
		            	->where('restaurant_id', $row->restaurant_id)
			            ->where('day_id', $row->day_id)
			            ->update(array('opening_time' => $row->opening_time_old, 'closing_time' => $row->closing_time_old));
        	}
        }
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments(){
		return array();
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions(){
		return array(
			array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
	}

}
