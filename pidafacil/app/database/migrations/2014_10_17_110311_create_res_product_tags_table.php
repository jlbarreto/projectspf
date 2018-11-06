<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateResProductTagsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('res_product_tags', function(Blueprint $table)
		{
			$table->integer('product_id')->unsigned();
			$table->integer('tag_id')->unsigned();
			$table->timestamps();

			$table->foreign('product_id')->references('product_id')->on('res_products');
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
		Schema::drop('res_product_tags');
	}

}
