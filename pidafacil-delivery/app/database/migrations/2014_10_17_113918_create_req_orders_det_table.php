<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateReqOrdersDetTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('req_orders_det', function(Blueprint $table)
		{
			$table->increments('order_det_id');
			$table->integer('order_id')->unsigned();
			$table->integer('product_id')->unsigned();
			$table->string ('product');
			$table->integer('quantity');
			$table->decimal('unit_price', 10, 2);
			$table->decimal('total_price', 10, 2);
			$table->text('comment');
			$table->timestamps();

			$table->foreign('order_id')->references('order_id')->on('req_orders');
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
		Schema::drop('req_orders_det');
	}

}
