<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateResWebContentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('res_web_content', function(Blueprint $table)
		{
			$table->increments('landing_page_id');
			$table->string('header',250);
			$table->string('logo',250);
			$table->string('banner',250);
			$table->string('slogan',45);
			$table->string('title_1',45);
			$table->text('text_1');
			$table->string('title_2',45);
			$table->text('text_2');
			$table->string('title_3',45);
			$table->text('text_3');
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
		Schema::drop('res_web_content');
	}

}
