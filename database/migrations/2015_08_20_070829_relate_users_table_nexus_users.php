<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RelateUsersTableNexusUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // to related classic nexus user profiles
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'nexusUser')) {


                $table->integer('nexus_id')->nullable();
                $table->foreign('nexus_id')->references('user_id')->on('userstable');
            }
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
            if (Schema::hasColumn('users', 'nexusUser')) {
                $table->dropColumn('nexusUser');
            }
        });
    }
}
