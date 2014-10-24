<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntriesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entries', function($table) {
            // Columns
            $table->increments('id');
            $table->integer('content_type_id')->unsigned()->index();
            $table->integer('entry_status_id')->unsigned()->index();
            $table->integer('author_id')->unsigned()->index();
            $table->string('title', 255);
            $table->string('route', 1000)->nullable();
            $table->string('meta_title', 100)->nullable();
            $table->string('meta_keywords', 300)->nullable();
            $table->string('meta_description', 250)->nullable();
            $table->boolean('templateFlag')->default(0);
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
        Schema::drop('entries');
    }

}
