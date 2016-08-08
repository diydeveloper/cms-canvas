<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CmsCanvasAddMediaTypeIdColumn extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('content_types', function(Blueprint $table) {
            $table->integer('media_type_id')->nullable()->unsigned()->index();
            $table->foreign('media_type_id')
                ->references('id')
                ->on('content_type_media_types')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('content_types', function (Blueprint $table) {
            $table->dropForeign('content_types_media_type_id');
            $table->dropColumn('media_type_id');
        });
    }

}
