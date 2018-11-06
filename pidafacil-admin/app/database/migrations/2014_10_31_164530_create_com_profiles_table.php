<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComProfilesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('com_profiles', function(Blueprint $table)
		{
			$table->increments('profile_id');
            $table->integer('user_id')->unsigned();
			$table->foreign('user_id')->references('user_id')->on('com_users');
            $table->string('username');
            $table->biginteger('uid')->unsigned();
            $table->string('access_token');
            $table->string('access_token_secret');
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
		Schema::drop('com_profiles');
	}

}
