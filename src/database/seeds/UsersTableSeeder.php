<?php 

namespace CmsCanvas\Database\Seeds;

use Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use CmsCanvas\Models\Timezone;

class UsersTableSeeder extends Seeder {

    public function run()
    {
        DB::table('users')->delete();

        $timezone = Timezone::where('identifier', 'US/Eastern')->first();

        DB::table('users')->insert([
            [
                'timezone_id' => $timezone->id, 
                'first_name' => 'Admin', 
                'last_name' => 'Admin',
                'email' => 'admin@domain.com',
                'password' => Hash::make('password'),
                'active' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }

}