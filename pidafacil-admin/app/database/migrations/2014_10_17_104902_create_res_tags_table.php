<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResTagsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('res_tags', function(Blueprint $table)
		{
			$table->integer('restaurant_id')->unsigned();
			$table->foreign('restaurant_id')->references('restaurant_id')->on('res_restaurants');
			$table->integer('tag_id')->unsigned();
			$table->foreign('tag_id')->references('tag_id')->on('com_tags');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('res_tags');
	}

}
