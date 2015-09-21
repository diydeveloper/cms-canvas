<?php 

namespace CmsCanvas\Database\Seeds;

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
            ['setting' => 'cms_canvas_installed', 'value' => '0'],
            ['setting' => 'site_name', 'value' => 'CMS Canvas'],
            ['setting' => 'theme', 'value' => 'default'],
            ['setting' => 'layout', 'value' => 'default'],
            ['setting' => 'notification_email', 'value' => ''],
            ['setting' => 'site_homepage', 'value' => $homePage->id],
            ['setting' => 'custom_404', 'value' => $custom404->id],
        ]);
    }

}
