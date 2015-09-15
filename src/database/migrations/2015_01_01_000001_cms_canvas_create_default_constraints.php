<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CmsCanvasCreateDefaultConstraints extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // users table constraints
        Schema::table('users', function(Blueprint $table)
        {
            $table->foreign('timezone_id')->references('id')->on('timezones');
        });

        // user_roles table constraints
        Schema::table('user_roles', function(Blueprint $table)
        {
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->onDelete('cascade');
        });

        // role_permissions table constraints
        Schema::table('role_permissions', function(Blueprint $table)
        {
            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->onDelete('cascade');

            $table->foreign('permission_id')
                ->references('id')
                ->on('permissions')
                ->onDelete('cascade');
        });

        // entries table constraints
        Schema::table('entries', function(Blueprint $table)
        {
            $table->foreign('content_type_id')
                ->references('id')
                ->on('content_types');

            $table->foreign('entry_status_id')
                ->references('id')
                ->on('entry_statuses');

            $table->foreign('author_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });

        // entry_data table constraints
        Schema::table('entry_data', function(Blueprint $table)
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
        Schema::table('content_type_fields', function(Blueprint $table)
        {
            $table->foreign('content_type_id')
                ->references('id')
                ->on('content_types')
                ->onDelete('cascade');

            $table->foreign('content_type_field_type_id')
                ->references('id')
                ->on('content_type_field_types');

        });

        // revisions table constraints
        Schema::table('revisions', function(Blueprint $table)
        {
            $table->foreign('resource_type_id')
                ->references('id')
                ->on('revision_resource_types');

            $table->foreign('content_type_id')
                ->references('id')
                ->on('content_types')
                ->onDelete('cascade');

            $table->foreign('author_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });

        // navigation_items table constraints
        Schema::table('navigation_items', function(Blueprint $table)
        {
            $table->foreign('navigation_id')
                ->references('id')
                ->on('navigations')
                ->onDelete('cascade');

            $table->foreign('parent_id')
                ->references('id')
                ->on('navigation_items')
                ->onDelete('cascade');

            $table->foreign('entry_id')
                ->references('id')
                ->on('entries');
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
        Schema::table('users', function(Blueprint $table)
        {
            $table->dropForeign('timezone_id');
        });

        // user_roles table constraints
        Schema::table('user_roles', function(Blueprint $table)
        {
            $table->dropForeign('user_id');
            $table->dropForeign('role_id');
        });

        // role_permissions table constraints
        Schema::table('role_permissions', function(Blueprint $table)
        {
            $table->dropForeign('role_id');
            $table->dropForeign('permission_id');
        });

        // entries table constraints
        Schema::table('entries', function(Blueprint $table)
        {
            $table->dropForeign('content_type_id');
            $table->dropForeign('entry_status_id');
            $table->dropForeign('author_id');
        });

        // entry_data table constraints
        Schema::table('entry_data', function(Blueprint $table)
        {
            $table->dropForeign('entry_id');
            $table->dropForeign('content_type_field_id');
            $table->dropForeign('language_id');
        });

        // content_type_fields table constraints
        Schema::table('content_type_fields', function(Blueprint $table)
        {
            $table->dropForeign('content_type_id');
            $table->dropForeign('content_type_field_type_id');
        });

        // revisions table constraints
        Schema::table('revisions', function(Blueprint $table)
        {
            $table->dropForeign('resource_type_id');
            $table->dropForeign('content_type_id');
            $table->dropForeign('author_id');
        });


        // navigation_items table constraints
        Schema::table('navigation_items', function(Blueprint $table)
        {
            $table->dropForeign('navigation_id');
            $table->dropForeign('parent_id');
            $table->dropForeign('entry_id');
        });
    }

}
