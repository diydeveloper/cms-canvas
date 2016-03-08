<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CmsCanvasCreateEntryDataTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entry_data', function(Blueprint $table) {
            // Columns
            $table->increments('id');
            $table->integer('entry_id')->unsigned()->index();
            $table->integer('content_type_field_id')->unsigned()->index();
            $table->string('content_type_field_short_tag', 50)->index();
            $table->string('language_locale', 5)->index();
            $table->longText('data')->nullable();
            $table->text('metadata')->nullable();
            $table->timestamps();

            $table->unique(['entry_id', 'content_type_field_id', 'language_locale']);
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
