<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateResSectionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('res_sections', function(Blueprint $table)
		{
			$table->increments('section_id');
			$table->integer('restaurant_id')->unsigned();
			$table->string('section',45);
			$table->integer('section_order_id');
			$table->string('banner',250);
			$table->timestamps();

			//referencias
			$table->foreign('restaurant_id')->references('restaurant_id')->on('res_restaurants');

		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('res_sections');
	}

}
