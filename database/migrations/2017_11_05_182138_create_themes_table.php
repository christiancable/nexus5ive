<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateThemesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('themes', function (Blueprint $table) {
            $table->increments('id')->unique()->unsigned();
            $table->string('path');
            $table->string('name')->unique();
            $table->timestamps();
        });

        // want a known timestamp so tests do not change the database each run
        $themeTimestamp = new Carbon('last day of October 1975', 'Europe/London');
        
        // create a default theme
        $theme = App\Theme::firstOrCreate([
            'name' => 'Default',
            'path' => '/css/app.css',
            'created_at' => $themeTimestamp->format('Y-m-d H:i:s'),
            'updated_at' => $themeTimestamp->format('Y-m-d H:i:s'),
         ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('themes');
    }
}
