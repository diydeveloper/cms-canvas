<?php 

namespace CmsCanvas\Database\Seeds;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class NavigationsTableSeeder extends Seeder {

    public function run()
    {
        DB::table('navigations')->delete();

        DB::table('navigations')->insert([
            [
                'title' => 'Main Menu', 
                'short_name' => 'main_menu',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }

}