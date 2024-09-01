<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->increments('id');
            $table->text('title');
            $table->mediumText('intro')->nullable();

            // relationships
            $table->integer('user_id')->unsigned();
            $table->integer('parent_id')->unsigned()->nullable();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')->onDelete('cascade');

            $table->foreign('parent_id')
                ->references('id')
                ->on('sections')->onDelete('cascade');
            // delete child sections when the parent is removed

            // $table->foreign('id')
            //     ->references('section_id')
            //     ->on('topics')->onDelete('cascade');

            $table->integer('weight')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('sections');
    }
}
