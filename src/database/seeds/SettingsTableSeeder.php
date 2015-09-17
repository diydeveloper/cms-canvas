<?php namespace CmsCanvas\Database\Seeds;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder {

    public function run()
    {
        DB::table('settings')->delete();

        DB::table('settings')->insert([
            ['setting' => 'site_name', 'value' => 'CMS Canvas'],
            ['setting' => 'theme', 'value' => 'default'],
            ['setting' => 'layout', 'value' => 'layouts.default'],
            ['setting' => 'custom_404', 'value' => '2'],
        ]);
    }

}