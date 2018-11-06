<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateResRestaurantsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('res_restaurants', function(Blueprint $table)
		{
			$table->increments('restaurant_id');
			$table->string('name',45);
			$table->integer('landing_page_id')->unsigned()->nullable();
			$table->integer('orders_allocator_id');
			$table->integer('parent_restaurant_id')->nullable();
			$table->integer('delivery_time');
			$table->boolean('guarantee_time');
			$table->decimal('shipping_cost');
			$table->decimal('minimum_order');
			$table->string('phone',45);
			$table->string('address',45);
			$table->string('map_coordinates',45);
			$table->integer('search_reserved_position');
			$table->integer('days_as_new');
			$table->string('slug',45);
			$table->timestamps();
			//referencias
			$table->foreign('landing_page_id')->references('landing_page_id')->on('res_web_content');
		

			

		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('res_restaurants');
	}

}
