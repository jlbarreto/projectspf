<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSecGroupsPermissionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sec_groups_permissions', function(Blueprint $table)
		{
			$table->integer('group_id')->unsigned();
			$table->foreign('group_id')->references('group_id')->on('sec_groups');
			$table->integer('permission_id')->unsigned();
			$table->foreign('permission_id')->references('permission_id')->on('sec_permissions');
			
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sec_groups_permissions');
	}

}
