<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddThemesToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // find the id of the default theme
        $defaultTheme = App\Theme::firstOrFail();
        Schema::table('users', function (Blueprint $table) use ($defaultTheme){
          $table->integer('theme_id')->unsigned()->default($defaultTheme->id);
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
             $table->dropColumn('theme_id');
        });
    }
}
