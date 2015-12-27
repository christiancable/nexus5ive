<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('user_id')->unsigned();
             $table->integer('author_id')->unsigned();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')->onDelete('cascade');

            $table->foreign('author_id')
                ->references('id')
                ->on('users')->onDelete('cascade');

            $table->text('text')->nullable();
            $table->boolean('read')->default(false);
            $table->timestamp('time');
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
        Schema::drop('messages');
    }
}
/*
mysql> describe nexusmessagetable;
+-----------------+---------------+------+-----+-------------------+-----------------------------+
| Field           | Type          | Null | Key | Default           | Extra                       |
+-----------------+---------------+------+-----+-------------------+-----------------------------+
| nexusmessage_id | int(11)       | NO   | PRI | NULL              | auto_increment              |
| user_id         | int(11)       | NO   | MUL | 0                 |                             |
| from_id         | int(11)       | NO   |     | 0                 |                             |
| text            | varchar(210)  | YES  |     | NULL              |                             |
| readstatus      | enum('y','n') | YES  |     | NULL              |                             |
| time            | timestamp     | NO   |     | CURRENT_TIMESTAMP | on update CURRENT_TIMESTAMP |
+-----------------+---------------+------+-----+-------------------+-----------------------------+
6 rows in set (0.00 sec)
 */
