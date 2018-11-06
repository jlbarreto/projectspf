<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateResRestaurantsServiceTypes extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('res_restaurants_service_types', function(Blueprint $table)
		{
			$table->integer('restaurant_id')->unsigned();
			$table->integer('service_type_id')->unsigned();
			$table->timestamps();

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
		Schema::drop('res_restaurants_service_types');
	}

}
