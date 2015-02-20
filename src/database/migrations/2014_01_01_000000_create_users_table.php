<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function($table) {
			// Columns
			$table->increments('id');
			$table->integer('timezone_id')->unsigned()->index();
			$table->string('first_name', 120);
			$table->string('last_name', 120);
			$table->string('email', 255);
			$table->string('password', 255);
			$table->string('phone', 25)->nullable();
			$table->string('address', 255)->nullable();
			$table->string('address2', 255)->nullable();
			$table->string('city', 255)->nullable();
			$table->string('state', 255)->nullable();
			$table->string('country', 255)->nullable();
			$table->string('zip', 25)->nullable();
			$table->boolean('active')->default(1);
			$table->datetime('last_login')->nullable();
			$table->string('remember_token', 100)->nullable();
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
		Schema::drop('users');
	}

}
