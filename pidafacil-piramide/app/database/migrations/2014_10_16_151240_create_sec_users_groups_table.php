<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSecUsersGroupsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sec_users_groups', function(Blueprint $table)
		{
			$table->integer('user_id')->unsigned();
			$table->foreign('user_id')->references('user_id')->on('com_users');
			$table->integer('group_id')->unsigned();
			$table->foreign('group_id')->references('group_id')->on('sec_groups');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sec_users_groups');
	}

}
