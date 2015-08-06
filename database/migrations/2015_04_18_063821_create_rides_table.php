<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRidesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('rides', function(Blueprint $table)
		{
			$table->increments('id');
            $table->unsignedInteger('utdb_id')
                  ->references('id')->on('ubers');
            $table->string('uuid');
            $table->string('urid')->unique();
            $table->string('product_id');
            $table->float('distance');
            $table->integer('request_time');
            $table->integer('start_time');
            $table->integer('end_time');
            $table->integer('ride_time');
            $table->integer('wait_time');
            $table->integer('distance_hour');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('rides');
	}

}
