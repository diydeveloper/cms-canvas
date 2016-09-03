<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CmsCanvasCreateContentTypeMediaTypesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('content_type_media_types', function(Blueprint $table) {
            // Columns
            $table->integer('id')->unsigned()->primary();
            $table->string('name', 50);
            $table->string('mime_type', 100);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('content_type_media_types');
    }

}
