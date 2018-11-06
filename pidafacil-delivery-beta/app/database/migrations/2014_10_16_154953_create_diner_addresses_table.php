<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDinerAddressesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('diner_addresses', function(Blueprint $table)
		{
			$table->increments('address_id');
			
			$table->integer('user_id')->unsigned();
			$table->foreign('user_id')->references('user_id')->on('com_users');
			$table->string('map_coordinates', 45);
			$table->string('address_name', 45);
			$table->string('address_1', 100);
			$table->string('address_2', 100)->nullable();
			$table->string('city', 45);
			$table->string('state', 45);
			$table->string('reference', 100)->nullable();
			$table->integer('country_id')->unsigned();
			$table->foreign('country_id')->references('country_id')->on('com_countries');			
			
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
		Schema::drop('diner_addresses');
	}

}
