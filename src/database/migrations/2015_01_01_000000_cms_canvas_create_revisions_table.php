<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CmsCanvasCreateRevisionsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('revisions', function(Blueprint $table) {
            // Columns
            $table->increments('id');
            $table->integer('resource_type_id')->unsigned()->index();
            $table->integer('resource_id')->unsigned()->index();
            $table->integer('content_type_id')->unsigned()->index();
            $table->integer('author_id')->unsigned()->index()->nullable();
            $table->string('author_name', 255);
            $table->longText('data');
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
        Schema::drop('revisions');
    }

}
