<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateResProductsConditionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('res_products_conditions', function(Blueprint $table)
		{
			$table->integer('product_id')->unsigned();
			$table->integer('condition_id')->unsigned();
			$table->integer('condition_order')->default(0);
			$table->timestamps();

			$table->foreign('product_id')->references('product_id')->on('res_products');
			$table->foreign('condition_id')->references('condition_id')->on('res_conditions');


		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('res_products_conditions');
	}

}
