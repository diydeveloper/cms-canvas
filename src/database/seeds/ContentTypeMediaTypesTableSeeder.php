<?php namespace CmsCanvas\Database\Seeds;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class ContentTypeMediaTypesTableSeeder extends Seeder {

    public function run()
    {
        DB::table('content_type_media_types')->delete();

        DB::table('content_type_media_types')->insert([
            ['id' => 1, 'name' => 'CSS', 'mime_type' => 'text/css'],
            ['id' => 2, 'name' => 'CSV', 'mime_type' => 'text/csv'],
            ['id' => 3, 'name' => 'HTML', 'mime_type' => 'text/html'],
            ['id' => 4, 'name' => 'Javascript', 'mime_type' => 'application/javascript'],
            ['id' => 5, 'name' => 'JSON', 'mime_type' => 'application/json'],
            ['id' => 6, 'name' => 'XML', 'mime_type' => 'application/xml'],
        ]);
    }

}