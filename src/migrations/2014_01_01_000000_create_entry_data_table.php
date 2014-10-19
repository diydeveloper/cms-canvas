<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntryDataTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entry_data', function($table) {
            // Columns
            $table->increments('id');
            $table->integer('entry_id')->unsigned()->index();
            $table->integer('content_type_field_id')->unsigned()->index();
            $table->integer('language_id')->unsigned()->index();
            $table->longText('data')->nullable();
            $table->text('metadata')->nullable();
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
        Schema::drop('entry_data');
    }

}
