<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // the primary section has no parent_id and therefore we cannot
        // constrain this relationship
        Schema::table('sections', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->foreign('parent_id')
                ->references('id')
                ->on('sections')->onDelete('cascade');
        });
    }
};
