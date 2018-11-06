<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComFavoriteTypesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('com_favorite_types', function(Blueprint $table)
		{
			$table->increments('favorite_type_id');
			$table->string('favorite_type', 45);
		});
	}

	/**
	 * Reverse the migrations. 
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('com_favorite_types');
	}

}
