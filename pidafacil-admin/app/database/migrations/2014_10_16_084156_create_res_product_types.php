<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateResProductTypes extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		/*
		Schema::create('res_product_types', function(Blueprint $table)
		{
			$table->increments('product_type_id');
			$table->string('product_type',45);
			$table->timestamps();
		});*/
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//Schema::drop('res_product_types');
	}

}
