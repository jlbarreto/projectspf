<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateResRestaurantsPaymentMethodsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('res_restaurants_payment_methods', function(Blueprint $table)
		{
			$table->integer('restaurant_id')->unsigned();
			$table->integer('payment_method_id')->unsigned();
			$table->timestamps();

			$table->foreign('restaurant_id')->references('restaurant_id')->on('res_restaurants');
			$table->foreign('payment_method_id')->references('payment_method_id')->on('res_payment_methods');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('res_restaurants_payment_methods');
	}

}
