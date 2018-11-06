<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateResProductConditionsOptionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('res_product_conditions_options', function(Blueprint $table)
		{
			$table->increments('condition_option_id');
			$table->string('condition_option',45);
			$table->integer('condition_id')->unsigned();

			$table->timestamps();

			$table->foreign('condition_id')->references('condition_id')->on('res_conditions');

		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('res_product_conditions_options');
	}

}
