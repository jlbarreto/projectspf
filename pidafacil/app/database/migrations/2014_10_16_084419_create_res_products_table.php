<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateResProductsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('res_products', function(Blueprint $table)
		{
			$table->increments('product_id');
			$table->string('product',45);
			$table->string('description');
			//$table->integer('product_type_id')->unsigned();
			$table->decimal('value');
			$table->integer('section_id')->unsigned();
			//$table->timestamp('registration_date');
			$table->string('slug',45);
			$table->integer('activate');


			$table->timestamps();
			//$table->foreign('product_type_id')->references('product_type_id')->on('res_product_types');
			$table->foreign('section_id')->references('section_id')->on('res_sections');

		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('res_products');
	}

}
