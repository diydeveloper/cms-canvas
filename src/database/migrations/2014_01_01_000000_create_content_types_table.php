<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContentTypesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('content_types', function($table) {
            // Columns
            $table->increments('id');
            $table->string('title', 255);
            $table->longText('layout')->nullable();
            $table->string('short_name', 50)->unique();
            $table->string('route', 500)->nullable();
            $table->string('route_prefix', 500)->nullable();
            $table->longText('page_head')->nullable();
            $table->integer('entries_allowed')->nullable()->unsigned();
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
        Schema::drop('content_types');
    }

}
