<?php 

namespace CmsCanvas\Database\Seeds;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use CmsCanvas\Models\Content\Entry;

class SettingsTableSeeder extends Seeder {

    public function run()
    {
        DB::table('settings')->delete();

        $homePage = Entry::where('title', 'Home')->first();
        $custom404 = Entry::where('title', 'Page Not Found')->first();

        DB::table('settings')->insert([
            [
                'id' => 1,
                'setting' => 'site_name', 
                'value' => 'CMS Canvas',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 2,
                'setting' => 'theme', 
                'value' => 'default',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 3,
                'setting' => 'layout', 
                'value' => 'default',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 4,
                'setting' => 'notification_email', 
                'value' => '',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 5,
                'setting' => 'site_homepage', 
                'value' => $homePage->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 6,
                'setting' => 'custom_404', 
                'value' => $custom404->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 7,
                'setting' => 'default_timezone', 
                'value' => 'US/Eastern',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 8,
                'setting' => 'ga_tracking_id', 
                'value' => '',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }

}
