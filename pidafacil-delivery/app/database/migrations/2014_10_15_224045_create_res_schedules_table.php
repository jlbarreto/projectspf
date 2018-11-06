<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateResSchedulesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('res_schedules', function(Blueprint $table)
		{
			$table->increments('schedule_id');
			$table->integer('restaurant_id')->unsigned();
			$table->integer('day_id');
			$table->time('opening_time');
			$table->time('closing_time');
			$table->integer('service_type_id')->unsigned();
			$table->timestamps();

			//referencias
			$table->foreign('restaurant_id')->references('restaurant_id')->on('res_restaurants');
			$table->foreign('service_type_id')->references('service_type_id')->on('res_service_types');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('res_schedules');
	}

}
