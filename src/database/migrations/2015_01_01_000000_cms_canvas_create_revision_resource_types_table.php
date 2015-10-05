<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CmsCanvasCreateRevisionResourceTypesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('revision_resource_types', function(Blueprint $table) {
            // Columns
            $table->integer('id')->unsigned()->primary();
            $table->string('name', 255);
            $table->string('key_name', 50)->unique();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('revision_resource_types');
    }

}
