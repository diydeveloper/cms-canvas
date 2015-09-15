<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CmsCanvasCreateNavigationItemsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('navigation_items', function(Blueprint $table) {
            // Columns
            $table->increments('id');
            $table->integer('navigation_id')->unsigned()->index();
            $table->integer('parent_id')->unsigned()->index()->default(0);
            $table->integer('entry_id')->unsigned()->index();
            $table->enum('type', ['page', 'url']);
            $table->string('title', 255)->nullable();
            $table->string('url', 500)->nullable();
            $table->string('id_attribute', 255)->nullable();
            $table->string('class_attribute', 255)->nullable();
            $table->string('target_attribute', 255)->nullable();
            $table->boolean('hidden_flag')->default(0);
            $table->integer('children_visibility_id')->unsigned()->index();
            $table->boolean('disable_current_flag')->default(0);
            $table->boolean('disable_current_ancestor_flag')->default(0);
            $table->integer('sort')->unsigned();
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
        Schema::drop('navigation_items');
    }

}
