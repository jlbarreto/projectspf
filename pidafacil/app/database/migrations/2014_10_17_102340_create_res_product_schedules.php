<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateResProductSchedules extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('res_product_schedules', function(Blueprint $table)
		{
			$table->increments('product_schedule_id');
			$table->integer('product_id')->unsigned();
			$table->integer('day_id');
			$table->time('opening_time');
			$table->time('closing_time');
			$table->timestamps();

			$table->foreign('product_id')->references('product_id')->on('res_products');



		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('res_product_schedules');
	}

}
