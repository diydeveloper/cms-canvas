<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CmsCanvasCreateEntriesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entries', function(Blueprint $table) {
            // Columns
            $table->increments('id');
            $table->integer('content_type_id')->unsigned()->index();
            $table->integer('entry_status_id')->unsigned()->index();
            $table->integer('author_id')->unsigned()->index()->nullable();
            $table->string('title', 255);
            $table->string('url_title', 500)->index()->nullable();
            $table->string('route', 1000)->nullable();
            $table->string('meta_title', 100)->nullable();
            $table->string('meta_keywords', 300)->nullable();
            $table->string('meta_description', 250)->nullable();
            $table->boolean('template_flag')->default(0);
            $table->datetime('created_at_local');
            $table->datetime('updated_at_local');
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
