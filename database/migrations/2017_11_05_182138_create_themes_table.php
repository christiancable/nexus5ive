<?php

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

        // create a default theme
         App\Theme::firstOrCreate([
            'name' => 'Default',
            'path' => '/css/app.css'
        ]);

        // add any other themes 
       // Artisan::call('db:seed', [
       //      '--class' => 'ThemesTableSeeder',
       //  ]);
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
