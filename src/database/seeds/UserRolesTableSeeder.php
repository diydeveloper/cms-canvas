<?php 

namespace CmsCanvas\Database\Seeds;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use CmsCanvas\Models\User;
use CmsCanvas\Models\Role;

class UserRolesTableSeeder extends Seeder {

    public function run()
    {
        DB::table('user_roles')->delete();

        $user = User::where('email', 'admin@domain.com')->first();
        $role = Role::where('name', 'Administrator')->first();

        DB::table('user_roles')->insert([
            [
                'user_id' => $user->id, 
                'role_id' => $role->id, 
            ],
        ]);
    }

}