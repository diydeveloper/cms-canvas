<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CmsCanvasCreateNavigationItemDataTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('navigation_item_data', function(Blueprint $table) {
            // Columns
            $table->increments('id');
            $table->integer('navigation_item_id')->unsigned()->index();
            $table->string('link_text', 255)->nullable();
            $table->string('language_locale', 5)->index();
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
        Schema::drop('navigation_item_data');
    }

}
