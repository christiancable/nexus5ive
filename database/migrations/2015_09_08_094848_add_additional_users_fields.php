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
            // in SQLite you have to define a default value for none-nullable cols when you are altering a table
            // https://laracasts.com/discuss/channels/general-discussion/migrations-sqlite-general-error-1-cannot-add-a-not-null-column-with-default-value-null
            $table->string('username')->unique()->default('New User');
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
        });        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('popname');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('about');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('location');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('administrator');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('banned');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('deleted');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('totalVisits');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('totalPosts');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('favouriteMusic');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('favouriteMovie');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('private');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('ipaddress');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('currentActivity');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('latestLogin');
        });
    }
}
