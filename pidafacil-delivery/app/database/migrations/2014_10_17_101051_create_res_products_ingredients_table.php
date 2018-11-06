<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateResProductsIngredientsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('res_products_ingredients', function(Blueprint $table)
		{
			$table->integer('product_id')->unsigned();
			$table->integer('ingredient_id')->unsigned();
			$table->integer('removable');
			$table->timestamps();

			$table->foreign('product_id')->references('product_id')->on('res_products');
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
		Schema::drop('res_products_ingredients');
	}

}
