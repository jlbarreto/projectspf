<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReqOrderRatingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('req_order_ratings', function(Blueprint $table)
		{
			$table->increments('order_rating_id');

			$table->integer('user_id')->unsigned();
			$table->foreign('user_id')->references('user_id')->on('com_users');
			$table->integer('order_id')->unsigned();
			$table->foreign('order_id')->references('order_id')->on('req_orders');
			$table->integer('quality_rating');
			$table->integer('speed_rating');
			$table->text('comment');
			$table->datetime('rating_date');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('req_order_ratings');
	}

}
