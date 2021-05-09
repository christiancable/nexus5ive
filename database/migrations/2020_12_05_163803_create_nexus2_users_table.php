<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNexus2UsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nexus2_users', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('Nick')->nullable();
            $table->string('UserId')->nullable();
            $table->string('RealName')->nullable();
            $table->string('PopName')->nullable();
            $table->string('Rights')->nullable();
            $table->string('NoOfEdits')->nullable();
            $table->string('TotalTimeOn')->nullable();
            $table->string('NoOfTimesOn')->nullable();
            $table->string('Password')->nullable();
            $table->string('Dept')->nullable();
            $table->string('Faculty')->nullable();
            $table->string('Created')->nullable();
            $table->string('LastOn')->nullable();
            $table->string('HistoryFile')->nullable();
            $table->string('BBSNo')->nullable();
            $table->string('Flags')->nullable();
            $table->mediumText('Info')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nexus2_users');
    }
}
