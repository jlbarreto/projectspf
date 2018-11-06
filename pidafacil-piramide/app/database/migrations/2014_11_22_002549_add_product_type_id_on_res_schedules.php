<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddProductTypeIdOnResSchedules extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('res_schedules', function(Blueprint $table)
		{
			/*
			$table->integer('service_type_id')->unsigned();
			$table->foreign('service_type_id')->references('service_type_id')->on('res_service_types');*/
			
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('res_schedules', function(Blueprint $table)
		{
			//Schema::drop('res_schedules');
			
		});
	}

}
