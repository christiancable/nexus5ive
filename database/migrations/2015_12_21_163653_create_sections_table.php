<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
                ->on('users');

             $table->foreign('parent_id')
                ->references('id')
                ->on('sections')->onDelete('cascade');
            // delete child sections when the parent is removed

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

/*
legacy sections table is
mysql> describe sectiontable;
+----------------+--------------+------+-----+---------+----------------+
| Field          | Type         | Null | Key | Default | Extra          |
+----------------+--------------+------+-----+---------+----------------+
| section_id     | int(11)      | NO   | PRI | NULL    | auto_increment |
| section_title  | varchar(50)  | YES  |     | NULL    |                |
| user_id        | int(11)      | YES  |     | NULL    |                |
| parent_id      | int(11)      | YES  | MUL | NULL    |                |
| section_weight | int(11)      | NO   |     | 0       |                |
| section_intro  | varchar(100) | YES  |     |         |                |
+----------------+--------------+------+-----+---------+----------------+
6 rows in set (0.00 sec)
 */