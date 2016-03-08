<?php 

namespace CmsCanvas\Database\Seeds;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use CmsCanvas\Models\Permission;
use CmsCanvas\Models\Role;

class RolePermissionsTableSeeder extends Seeder {

    public function run()
    {
        DB::table('role_permissions')->delete();

        $role = Role::where('name', 'Administrator')->first();
        $permissions = Permission::where('key_name', 'like', 'ADMIN%')->get();

        $inserts = [];
        foreach ($permissions as $permission) {
            $inserts[] = [
                'role_id' => $role->id, 
                'permission_id' => $permission->id, 
            ];
        }

        DB::table('role_permissions')->insert($inserts);
    }

}