<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->increments('id');
            $table->text('text')->nullable();
            $table->boolean('read')->default(false);

            // relationships
            $table->integer('author_id')->unsigned();
            $table->integer('user_id')->unsigned();
            
            $table->foreign('author_id')
                ->references('id')
                ->on('users');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')->onDelete('cascade');
            // delete comments when we remove the user

            // laravel timestamsp
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
        Schema::drop('comments');
    }
}

/*
legacy commentstable to migrate into this
+------------+---------------+------+-----+---------+----------------+
| Field      | Type          | Null | Key | Default | Extra          |
+------------+---------------+------+-----+---------+----------------+
| comment_id | int(11)       | NO   | PRI | NULL    | auto_increment |
| user_id    | int(11)       | NO   | MUL | 0       |                |
| from_id    | int(11)       | NO   |     | 0       |                |
| text       | varchar(200)  | YES  |     | NULL    |                |
| readstatus | enum('y','n') | YES  |     | NULL    |                |
+------------+---------------+------+-----+---------+----------------+
*/
