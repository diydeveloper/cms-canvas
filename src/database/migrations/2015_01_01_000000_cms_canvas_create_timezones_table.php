<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CmsCanvasCreateTimezonesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('timezones', function(Blueprint $table) {
            // Columns
            $table->increments('id');
            $table->string('name', 255);
            $table->string('identifier', 255);
            $table->string('offset', 8);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('timezones');
    }

}
