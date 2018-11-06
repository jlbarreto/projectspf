<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDinerTagsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('diner_tags', function(Blueprint $table)
		{
			$table->integer('user_id')->unsigned();
			$table->foreign('user_id')->references('user_id')->on('com_users');
			$table->integer('tag_id')->unsigned();
			$table->foreign('tag_id')->references('tag_id')->on('com_tags');
			$table->integer('counter');
			$table->datetime('tag_date');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('diner_tags');
	}

}
