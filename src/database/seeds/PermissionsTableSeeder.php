<?php namespace CmsCanvas\Database\Seeds;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder {

    public function run()
    {
        DB::table('permissions')->delete();

        DB::table('permissions')->insert([
            ['name' => 'Admin - Access', 'key_name' => 'ADMIN', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Admin - Entry View', 'key_name' => 'ADMIN_ENTRY_VIEW', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Admin - Entry Edit', 'key_name' => 'ADMIN_ENTRY_EDIT', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Admin - User View', 'key_name' => 'ADMIN_USER_VIEW', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Admin - User Edit', 'key_name' => 'ADMIN_USER_EDIT', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Admin - Role View', 'key_name' => 'ADMIN_ROLE_VIEW', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Admin - Role Edit', 'key_name' => 'ADMIN_ROLE_EDIT', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Admin - Content Type View', 'key_name' => 'ADMIN_CONTENT_TYPE_VIEW', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Admin - Content Type Edit', 'key_name' => 'ADMIN_CONTENT_TYPE_EDIT', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Admin - User Create', 'key_name' => 'ADMIN_USER_CREATE', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Admin - User Delete', 'key_name' => 'ADMIN_USER_DELETE', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Admin - Impersonate User', 'key_name' => 'ADMIN_IMPERSONATE_USER', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Admin - Role Create', 'key_name' => 'ADMIN_ROLE_CREATE', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Admin - Role Delete', 'key_name' => 'ADMIN_ROLE_DELETE', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Admin - Permission View', 'key_name' => 'ADMIN_PERMISSION_VIEW', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Admin - Admin - Permission Edit', 'key_name' => 'ADMIN_PERMISSION_EDIT', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Admin - Admin - Permission Create', 'key_name' => 'ADMIN_PERMISSION_CREATE', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Admin - Admin - Permission Delete', 'key_name' => 'ADMIN_PERMISSION_DELETE', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Admin - Admin - Navigation View', 'key_name' => 'ADMIN_NAVIGATION_VIEW', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Admin - Admin - Navigation Create', 'key_name' => 'ADMIN_NAVIGATION_CREATE', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Admin - Admin - Navigation Edit', 'key_name' => 'ADMIN_NAVIGATION_EDIT', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Admin - Admin - Navigation Delete', 'key_name' => 'ADMIN_NAVIGATION_DELETE', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Admin - Admin - Entry Create', 'key_name' => 'ADMIN_ENTRY_CREATE', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Admin - Admin - Entry Delete', 'key_name' => 'ADMIN_ENTRY_DELETE', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Admin - Admin - Content Type Create', 'key_name' => 'ADMIN_CONTENT_TYPE_CREATE', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Admin - Admin - Content Type Delete', 'key_name' => 'ADMIN_CONTENT_TYPE_DELETE', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Admin - Admin - Content Type Field View', 'key_name' => 'ADMIN_CONTENT_TYPE_FIELD_VIEW', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Admin - Admin - Content Type Field Edit', 'key_name' => 'ADMIN_CONTENT_TYPE_FIELD_EDIT', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Admin - Admin - Content Type Field Create', 'key_name' => 'ADMIN_CONTENT_TYPE_FIELD_CREATE', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Admin - Admin - Content Type Field Delete', 'key_name' => 'ADMIN_CONTENT_TYPE_FIELD_DELETE', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);
    }

}