<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTopicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('topics', function (Blueprint $table) {

            // how can we do a cascading delete on the section so that it removes child topics?

            $table->increments('id');

            $table->text('title');
            $table->mediumText('intro')->nullable();

            // relationships
            $table->integer('section_id')->unsigned();

            $table->foreign('section_id')
                ->references('id')
                ->on('sections')->onDelete('cascade');

            $table->boolean('secret')->default(false);
            $table->boolean('readonly')->default(false);

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
        Schema::drop('topics');
    }
}

/* was
mysql> describe topictable;
+--------------------+---------------+------+-----+---------+----------------+
| Field              | Type          | Null | Key | Default | Extra          |
+--------------------+---------------+------+-----+---------+----------------+
| topic_id           | int(11)       | NO   | PRI | NULL    | auto_increment |
| topic_title        | varchar(50)   | YES  |     | NULL    |                |
| section_id         | int(11)       | YES  | MUL | NULL    |                |
| topic_description  | mediumtext    | YES  |     | NULL    |                |
| topic_annon        | enum('y','n') | YES  |     | n       |                |
| topic_readonly     | enum('y','n') | YES  |     | n       |                |
| topic_weight       | tinyint(4)    | YES  |     | 10      |                |
| topic_title_hidden | enum('y','n') | YES  |     | n       |                |
+--------------------+---------------+------+-----+---------+----------------+
8 rows in set (0.01 sec)
*/
