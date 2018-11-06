<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComTagsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('com_tags', function(Blueprint $table)
		{
			$table->increments('tag_id');
			$table->string('tag_name', 45);
			$table->integer('tag_type_id')->unsigned();
			$table->foreign('tag_type_id')->references('tag_type_id')->on('com_tag_types');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('com_tags');
	}

}
