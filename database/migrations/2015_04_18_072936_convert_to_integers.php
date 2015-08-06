<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ConvertToIntegers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('ubers', function(Blueprint $table)
		{
			$table->integer('rides_count')->change();
            $table->integer('miles_driven')->change();
            $table->integer('total_time')->change();
            $table->integer('wait_time')->change();
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
