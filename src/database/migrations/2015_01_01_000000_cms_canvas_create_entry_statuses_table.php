<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CmsCanvasCreateEntryStatusesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entry_statuses', function(Blueprint $table) {
            // Columns
            $table->integer('id')->unsigned()->primary();
            $table->string('name', 50);
            $table->string('key_name', 25)->unique();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('entry_statuses');
    }

}
