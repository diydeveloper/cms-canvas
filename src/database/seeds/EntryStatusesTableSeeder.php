<?php namespace CmsCanvas\Database\Seeds;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class EntryStatusesTableSeeder extends Seeder {

    public function run()
    {
        DB::table('entry_statuses')->delete();

        DB::table('entry_statuses')->insert([
            ['id' => 1, 'name' => 'Published', 'key_name' => 'PUBLISHED'],
            ['id' => 2, 'name' => 'Draft', 'key_name' => 'DRAFT'],
            ['id' => 3, 'name' => 'Disabled', 'key_name' => 'DISABLED'],
        ]);
    }

}