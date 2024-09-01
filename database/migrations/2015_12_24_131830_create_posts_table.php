<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');

            $table->text('title')->nullable();
            $table->longText('text')->nullable();
            $table->timestamp('time');
            $table->text('popname')->nullable();
            $table->boolean('html')->default(false);

            $table->integer('user_id')->unsigned();
            $table->integer('topic_id')->unsigned();
            $table->integer('update_user_id')->unsigned()->nullable()->default(null);

            $table->foreign('user_id')
                ->references('id')
                ->on('users')->onDelete('cascade');

            $table->foreign('topic_id')
                ->references('id')
                ->on('topics')->onDelete('cascade');

            // I don't want an updated post to be removed if the last person to update it has
            // been deleted so we'll remove this relationship when the user is deleting
            // $table->foreign('update_user_id')
            //     ->references('id')
            //     ->on('users');

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
        Schema::drop('posts');
    }
}
/*
mysql> describe messagetable;
+-----------------+-------------+------+-----+-------------------+-----------------------------+
| Field           | Type        | Null | Key | Default           | Extra                       |
+-----------------+-------------+------+-----+-------------------+-----------------------------+
| message_id      | int(11)     | NO   | PRI | NULL              | auto_increment              |
| message_text    | mediumtext  | YES  |     | NULL              |                             |
| topic_id        | int(11)     | NO   | MUL | 0                 |                             |
| user_id         | int(11)     | NO   |     | 0                 |                             |
| message_title   | varchar(50) | YES  |     | NULL              |                             |
| message_time    | timestamp   | NO   | MUL | CURRENT_TIMESTAMP | on update CURRENT_TIMESTAMP |
| message_popname | varchar(70) | YES  |     | NULL              |                             |
| message_html    | tinyint(1)  | YES  |     | 0                 |                             |
| update_user_id  | int(11)     | YES  |     | 0                 |                             |
+-----------------+-------------+------+-----+-------------------+-----------------------------+
9 rows in set (0.00 sec)
 */
