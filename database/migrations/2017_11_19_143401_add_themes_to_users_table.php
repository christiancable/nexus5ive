<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Theme;

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
        $defaultTheme = Theme::firstOrFail();
        Schema::table('users', function (Blueprint $table) use ($defaultTheme) {
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
