<?php namespace CmsCanvas\Database\Seeds;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class EntryStatusesTableSeeder extends Seeder {

    public function run()
    {
        DB::table('entry_statuses')->delete();

        DB::table('entry_statuses')->insert([
            ['name' => 'Published', 'key_name' => 'PUBLISHED'],
            ['name' => 'Draft', 'key_name' => 'DRAFT'],
            ['name' => 'Disabled', 'key_name' => 'DISABLED'],
        ]);
    }

}