<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CmsCanvasCreateContentTypeFieldsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('content_type_fields', function(Blueprint $table) {
            // Columns
            $table->increments('id');
            $table->integer('content_type_id')->unsigned()->index();
            $table->integer('content_type_field_type_id')->unsigned()->index();
            $table->string('label', 50);
            $table->string('short_tag', 50)->index();
            $table->boolean('translate')->default(0);
            $table->boolean('required')->default(0);
            $table->text('settings')->nullable();
            $table->text('options')->nullable();
            $table->integer('sort')->unsigned()->nullable();
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
        Schema::drop('content_type_fields');
    }

}
