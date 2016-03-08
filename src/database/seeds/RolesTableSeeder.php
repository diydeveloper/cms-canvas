<?php namespace CmsCanvas\Database\Seeds;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder {

    public function run()
    {
        DB::table('roles')->delete();

        DB::table('roles')->insert([
            'name' => 'Administrator',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }

}