<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResRestaurantsUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('res_restaurants_users', function(Blueprint $table)
		{
			$table->integer('restaurant_id')->unsigned();
			$table->foreign('restaurant_id')->references('restaurant_id')->on('res_restaurants');
			$table->integer('user_id')->unsigned();
			$table->foreign('user_id')->references('user_id')->on('com_users');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('res_restaurants_users');
	}

}
