<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDinerFavoritesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('diner_favorites', function(Blueprint $table)
		{
			$table->increments('favorite_id');

			$table->integer('user_id')->unsigned();
			$table->foreign('user_id')->references('user_id')->on('com_users');
			$table->integer('favorite_type_id')->unsigned();
			$table->foreign('favorite_type_id')->references('favorite_type_id')->on('com_favorite_types');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('diner_favorites');
	}

}
