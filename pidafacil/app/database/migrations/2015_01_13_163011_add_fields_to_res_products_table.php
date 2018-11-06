<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToResProductsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('res_products', function($table)
            {
                $table->boolean('promotion')->nullable();
                $table->dateTime('start_date')->nullable();
                $table->dateTime('end_date')->nullable();
                $table->dateTime('image_web')->nullable();
                $table->dateTime('image_app')->nullable();
            });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
	}

}
