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

        // content_types table constraints
        Schema::table('content_types', function(Blueprint $table)
        {
            $table->foreign('admin_entry_view_permission_id')
                ->references('id')
                ->on('permissions')
                ->onDelete('set null');

            $table->foreign('admin_entry_edit_permission_id')
                ->references('id')
                ->on('permissions')
                ->onDelete('set null');

            $table->foreign('admin_entry_create_permission_id')
                ->references('id')
                ->on('permissions')
                ->onDelete('set null');

            $table->foreign('admin_entry_delete_permission_id')
                ->references('id')
                ->on('permissions')
                ->onDelete('set null');
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
            $table->dropForeign('users_timezone_id_foreign');
        });

        // user_roles table constraints
        Schema::table('user_roles', function(Blueprint $table)
        {
            $table->dropForeign('user_roles_user_id_foreign');
            $table->dropForeign('user_roles_role_id_foreign');
        });

        // role_permissions table constraints
        Schema::table('role_permissions', function(Blueprint $table)
        {
            $table->dropForeign('role_permissions_role_id_foreign');
            $table->dropForeign('role_permissions_permission_id_foreign');
        });

        // content_types table constraints
        Schema::table('content_types', function(Blueprint $table)
        {
            $table->dropForeign('content_types_admin_entry_view_permission_id_foreign');
            $table->dropForeign('content_types_admin_entry_edit_permission_id_foreign');
            $table->dropForeign('content_types_admin_entry_create_permission_id_foreign');
            $table->dropForeign('content_types_admin_entry_delete_permission_id_foreign');
        });

        // entries table constraints
        Schema::table('entries', function(Blueprint $table)
        {
            $table->dropForeign('entries_content_type_id_foreign');
            $table->dropForeign('entries_entry_status_id_foreign');
            $table->dropForeign('entries_author_id_foreign');
        });

        // entry_data table constraints
        Schema::table('entry_data', function(Blueprint $table)
        {
            $table->dropForeign('entry_data_entry_id_foreign');
            $table->dropForeign('entry_data_content_type_field_id_foreign');
            $table->dropForeign('entry_data_language_id_foreign');
        });

        // content_type_fields table constraints
        Schema::table('content_type_fields', function(Blueprint $table)
        {
            $table->dropForeign('content_type_fields_content_type_id_foreign');
            $table->dropForeign('content_type_fields_content_type_field_type_id_foreign');
        });

        // revisions table constraints
        Schema::table('revisions', function(Blueprint $table)
        {
            $table->dropForeign('revisions_resource_type_id_foreign');
            $table->dropForeign('revisions_content_type_id_foreign');
            $table->dropForeign('revisions_author_id_foreign');
        });


        // navigation_items table constraints
        Schema::table('navigation_items', function(Blueprint $table)
        {
            $table->dropForeign('navigation_items_navigation_id_foreign');
            $table->dropForeign('navigation_items_parent_id_foreign');
            $table->dropForeign('navigation_items_entry_id_foreign');
        });
    }

}
