<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateResRestaurantConditionsOptionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('res_restaurant_conditions_options', function(Blueprint $table)
		{
			$table->increments('condition_option_id')->unsigned();

			$table->integer('restaurant_id')->unsigned();

			$table->timestamps();

			$table->foreign('condition_option_id')->references('condition_option_id')->on('res_product_conditions_options');
			$table->foreign('restaurant_id')->references('restaurant_id')->on('res_restaurants');

		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('res_restaurant_conditions_options');
	}

}
