<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReqOrderStatusLogsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('req_order_status_logs', function(Blueprint $table)
		{
			$table->increments('status_log_id');
				
			$table->integer('order_id')->unsigned();
			$table->foreign('order_id')->references('order_id')->on('req_orders');
			$table->integer('order_status_id')->unsigned();
			$table->foreign('order_status_id')->references('order_status_id')->on('req_order_status');
			$table->integer('user_id')->unsigned();
			$table->foreign('user_id')->references('user_id')->on('com_users');
			$table->text   ('comment');
			$table->timestamps();
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('req_order_status_logs');
	}

}
