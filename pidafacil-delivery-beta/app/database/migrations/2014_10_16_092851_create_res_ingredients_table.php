<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateResIngredientsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('res_ingredients', function(Blueprint $table)
		{
			$table->increments('ingredient_id');
			$table->string('ingredient',45);
			$table->integer('restaurant_id')->unsigned();
			$table->timestamps();

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
		Schema::drop('res_ingredients');
	}

}
