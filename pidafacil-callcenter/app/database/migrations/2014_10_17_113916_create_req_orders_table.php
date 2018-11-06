<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReqOrdersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('req_orders', function(Blueprint $table)
		{
			$table->increments('order_id');
			
			/*
			 * $table->integer('address_id')->unsigned()->nullable();
			 * $table->foreign('address_id')->references('address_id')->on('diner_addresses');
			 */
			$table->integer('restaurant_id')->unsigned();
			$table->foreign('restaurant_id')->references('restaurant_id')->on('res_restaurants');
			/*
			 * $table->integer('order_status_id')->unsigned();
			 * $table->foreign('order_status_id')->references('order_status_id')->on('req_order_status');
			 */
			$table->integer('service_type_id')->unsigned()->nullable();
			$table->foreign('service_type_id')->references('service_type_id')->on('res_service_types');
			$table->text   ('address');
			$table->integer('pickup_hour')->nullable();
			$table->integer('pickup_min')->nullable();
			$table->integer('payment_method_id')->unsigned()->nullable();
			$table->foreign('payment_method_id')->references('payment_method_id')->on('res_payment_methods');
			$table->decimal('order_total', 10, 2);
			$table->integer('pay_bill')->nullable();
			$table->decimal('pay_change', 10, 2)->nullable();
			$table->boolean('credit_denied')->nullable();
			$table->text   ('credit_authorization')->nullable();
			$table->string ('credit_name')->nullable();
			$table->string ('credit_card')->nullable();
			$table->integer('credit_expmonth')->nullable();
			$table->integer('credit_expyear')->nullable();
			$table->integer('secure_code')->nullable();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('req_orders');
	}

}
