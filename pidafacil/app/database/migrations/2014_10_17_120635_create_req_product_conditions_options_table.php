<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateReqProductConditionsOptionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('req_product_conditions_options', function(Blueprint $table)
		{
			$table->increments('product_contion_option_id');
			$table->integer('order_det_id')->unsigned();
			$table->integer('condition_id')->unsigned();
			$table->string('condition');
			$table->integer('condition_option_id')->unsigned();
			$table->string('condition_option');
			$table->timestamps();
			
			$table->foreign('order_det_id')->references('order_det_id')->on('req_orders_det');
			$table->foreign('condition_id')->references('condition_id')->on('res_conditions');
			$table->foreign('condition_option_id')->references('condition_option_id')->on('res_product_conditions_options');

		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('req_product_conditions_options');
	}

}
