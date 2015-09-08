<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdditionalUsersFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
			$table->string('username')->unique();
			$table->string('popname')->nullable()->default('New User');
			$table->mediumText('about')->nullable();
			$table->string('location')->nullable()->default('Someplace');
			$table->boolean('administrator')->default(false);
            //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
			$table->dropColumn('username');
			$table->dropColumn('popname');
			$table->dropColumn('about');
			$table->dropColumn('location');
			$table->dropColumn('administrator');
            //
        });
    }
}
