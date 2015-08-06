<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUbersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ubers', function(Blueprint $table)
		{
			$table->increments('id');
            $table->string('utid');
            $table->boolean('public')->default(true);
			$table->string('uuid');
			$table->string('access_token');
            $table->string('refresh_token');
            $table->string('photo');
            $table->string('name');
            $table->string('rides_count');
            $table->string('miles_driven');
            $table->string('total_time');
            $table->string('wait_time');
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
		Schema::drop('ubers');
	}

}
