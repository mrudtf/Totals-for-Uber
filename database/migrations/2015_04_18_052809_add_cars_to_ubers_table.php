<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCarsToUbersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('ubers', function(Blueprint $table)
		{
            $table->integer('uberx_count');
            $table->integer('uberblack_count');
            $table->integer('uberpool_count');
            $table->integer('ubertaxi_count');
            $table->integer('ubersuv_count');
            $table->integer('uberxl_count');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('ubers', function(Blueprint $table)
		{
			//
		});
	}

}
