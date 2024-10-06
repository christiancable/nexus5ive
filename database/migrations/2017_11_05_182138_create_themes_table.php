<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Theme;

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

        // TODO add this to a seed
        
        // want a known timestamp so tests do not change the database each run
        $themeTimestamp = new Carbon('last day of October 1975', 'Europe/London');

        // create a default theme
        $theme = Theme::firstOrCreate([
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
