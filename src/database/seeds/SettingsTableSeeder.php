<?php namespace CmsCanvas\Database\Seeds;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder {

    public function run()
    {
        DB::table('settings')->delete();

        DB::table('settings')->insert(array(
            array('setting' => 'site_name', 'value' => 'CMS Canvas'),
            array('setting' => 'theme', 'value' => 'default'),
            array('setting' => 'layout', 'value' => 'layouts.default'),
            array('setting' => 'custom_404', 'value' => '2'),
        ));
    }

}