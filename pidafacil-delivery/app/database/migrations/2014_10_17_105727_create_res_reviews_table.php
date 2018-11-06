<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResReviewsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('res_reviews', function(Blueprint $table)
		{
			$table->increments('review_id');

			$table->integer('user_id')->unsigned();
			$table->foreign('user_id')->references('user_id')->on('com_users');
			$table->integer('restaurant_id')->unsigned();
			$table->foreign('restaurant_id')->references('restaurant_id')->on('res_restaurants');
			$table->integer('quality_rating');
			$table->integer('speed_rating');
			$table->text('review');
			$table->datetime('review_date');
			
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('res_reviews');
	}

}
