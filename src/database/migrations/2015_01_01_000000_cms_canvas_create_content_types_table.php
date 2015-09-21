<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CmsCanvasCreateContentTypesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('content_types', function(Blueprint $table) {
            // Columns
            $table->increments('id');
            $table->string('title', 255);
            $table->longText('layout')->nullable();
            $table->string('short_name', 50)->unique();
            $table->string('route', 500)->nullable();
            $table->longText('page_head')->nullable();
            $table->integer('entries_allowed')->nullable()->unsigned();
            $table->integer('max_revisions')->nullable()->unsigned();
            $table->string('theme_layout', 100)->nullable();
            $table->boolean('dynamic_routing_flag')->default(0);
            $table->integer('admin_entry_view_permission_id')->nullable()->unsigned();
            $table->integer('admin_entry_edit_permission_id')->nullable()->unsigned();
            $table->integer('admin_entry_create_permission_id')->nullable()->unsigned();
            $table->integer('admin_entry_delete_permission_id')->nullable()->unsigned();
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
