<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDefaultConstraints extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // users table constraints
        Schema::table('users', function($table)
        {
            $table->foreign('timezone_id')->references('id')->on('timezones');
        });

        // entries table constraints
        Schema::table('entries', function($table)
        {
            $table->foreign('content_type_id')->references('id')->on('content_types');
            $table->foreign('entry_status_id')->references('id')->on('entry_statuses');
            $table->foreign('author_id')->references('id')->on('users');
        });

        // entry_data table constraints
        Schema::table('entry_data', function($table)
        {
            $table->foreign('entry_id')
                ->references('id')
                ->on('entries')
                ->onDelete('cascade');

            $table->foreign('content_type_field_id')
                ->references('id')
                ->on('content_type_fields')
                ->onDelete('cascade');

            $table->foreign('language_id')
                ->references('id')
                ->on('languages')
                ->onDelete('cascade');
        });

        // content_type_fields table constraints
        Schema::table('content_type_fields', function($table)
        {
            $table->foreign('content_type_id')->references('id')->on('content_types')->onDelete('cascade');
            $table->foreign('content_type_field_type_id')->references('id')->on('content_type_field_types');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // users table constraints
        Schema::table('users', function($table)
        {
            $table->dropForeign('timezone_id');
        });

        // entries table constraints
        Schema::table('entries', function($table)
        {
            $table->dropForeign('content_type_id');
            $table->dropForeign('entry_status_id');
            $table->dropForeign('author_id');
        });

        // entry_data table constraints
        Schema::table('entry_data', function($table)
        {
            $table->dropForeign('entry_id');
            $table->dropForeign('content_type_field_id');
            $table->dropForeign('language_id');
        });

        // content_type_fields table constraints
        Schema::table('content_type_fields', function($table)
        {
            $table->dropForeign('content_type_id');
            $table->dropForeign('content_type_field_type_id');

        });
    }

}
