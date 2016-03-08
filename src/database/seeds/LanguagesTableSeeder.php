<?php namespace CmsCanvas\Database\Seeds;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class LanguagesTableSeeder extends Seeder {

    public function run()
    {
        DB::table('languages')->delete();

        DB::table('languages')->insert([
            'language' => 'English',
            'locale' => 'en',
            'default' => 1,
            'active' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }

}