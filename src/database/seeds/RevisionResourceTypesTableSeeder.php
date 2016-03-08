<?php namespace CmsCanvas\Database\Seeds;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class RevisionResourceTypesTableSeeder extends Seeder {

    public function run()
    {
        DB::table('revisions')->delete();
        DB::table('revision_resource_types')->delete();

        DB::table('revision_resource_types')->insert([
            ['id' => 1, 'name' => 'Entry', 'key_name' => 'ENTRY'],
            ['id' => 2, 'name' => 'Content Type', 'key_name' => 'CONTENT_TYPE'],
        ]);
    }

}