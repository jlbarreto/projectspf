<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateReqProductIngredients extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('req_product_ingredients', function(Blueprint $table)
		{
			$table->increments('product_ingredien_id');
			$table->integer('order_det_id')->unsigned();
			$table->integer('ingredient_id')->unsigned();
			$table->string ('ingredient');
			$table->integer('remove');
			$table->timestamps();

			$table->foreign('order_det_id')->references('order_det_id')->on('req_orders_det');
			$table->foreign('ingredient_id')->references('ingredient_id')->on('res_ingredients');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('req_product_ingredients');
	}

}
