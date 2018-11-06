<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('com_users', function(Blueprint $table)
		{
			$table->increments('user_id');

			$table->string('email', 45);
			$table->string('password', 45);
			$table->string('name', 45);
			$table->string('last_name', 45);
			$table->boolean('gender');
			$table->datetime('birth_date');
			$table->integer('country_id')->unsigned();
			$table->foreign('country_id')->references('country_id')->on('com_countries');
			$table->string('phone', 45);
			$table->tinyInteger('status');
			$table->boolean('terms_acceptance');

			$table->rememberToken();
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
		Schema::drop('com_users');
	}

}
