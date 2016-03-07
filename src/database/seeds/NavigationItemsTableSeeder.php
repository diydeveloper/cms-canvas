<?php 

namespace CmsCanvas\Database\Seeds;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use CmsCanvas\Models\Content\Entry;
use CmsCanvas\Models\Content\Navigation;

class NavigationItemsTableSeeder extends Seeder {

    public function run()
    {
        DB::table('navigation_items')->delete();

        $navigation = Navigation::where('short_name', 'main_menu')->first();
        $entry = Entry::where('title', 'Home')->first();

        DB::table('navigation_items')->insert([
            [
                'title' => 'Home', 
                'navigation_id' => $navigation->id, 
                'entry_id' => $entry->id, 
                'type' => 'page',
                'url' => null, 
                'current_uri_pattern' => null, 
                'children_visibility_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Blog', 
                'navigation_id' => $navigation->id, 
                'entry_id' => null, 
                'type' => 'url',
                'url' => '/blog', 
                'current_uri_pattern' => '(blog|blog/.*)', 
                'children_visibility_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }

}