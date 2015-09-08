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
			$table->boolean('banned')->default(false);
			$table->boolean('deleted')->default(false);
			$table->integer('totalVisits')->default(0);
			$table->integer('totalPosts')->default(0);
			$table->string('favouriteMovie')->nullable();
			$table->string('favouriteMusic')->nullable();
			$table->boolean('private')->default(true);
			$table->string('ipaddress')->nullable()->default('127.0.0.1');
			$table->string('currentActivity')->nullable();
			$table->timestamp('latestLogin')->nullable();
			
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
			$table->dropColumn('banned');
			$table->dropColumn('deleted');			
			$table->dropColumn('totalVisits');
			$table->dropColumn('totalPosts');
			$table->dropColumn('favouriteMusic');
			$table->dropColumn('favouriteMovie');
			$table->dropColumn('private');
			$table->dropColumn('ipaddress');
			$table->dropColumn('currentActivity');
			$table->dropColumn('latestLogin');
            //
        });
    }
}
