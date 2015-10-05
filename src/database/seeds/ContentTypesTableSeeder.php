<?php 

namespace CmsCanvas\Database\Seeds;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class ContentTypesTableSeeder extends Seeder {

    public function run()
    {
        DB::table('entries')->delete();
        DB::table('content_type_fields')->delete();
        DB::table('content_types')->delete();

        DB::table('content_types')->insert([
            [
                'title' => 'Page', 
                'layout' => '{{ content }}', 
                'short_name' => 'page',
                'max_revisions' => 5,
                'theme_layout' => 'default',
                'dynamic_routing_flag' => 'default',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }

}