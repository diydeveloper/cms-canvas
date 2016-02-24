<?php namespace CmsCanvas\Database\Seeds;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class ContentTypeFieldTypesTableSeeder extends Seeder {

    public function run()
    {
        DB::table('content_type_field_types')->delete();

        DB::table('content_type_field_types')->insert([
            ['name' => 'CKEditor', 'key_name' => 'CKEDITOR'],
            ['name' => 'Text', 'key_name' => 'TEXT'],
            ['name' => 'Checkbox', 'key_name' => 'CHECKBOX'],
            ['name' => 'Image', 'key_name' => 'IMAGE'],
            ['name' => 'Date', 'key_name' => 'DATE'],
            ['name' => 'Date Time', 'key_name' => 'DATETIME'],
            ['name' => 'Dropdown', 'key_name' => 'DROPDOWN'],
            ['name' => 'Textarea', 'key_name' => 'TEXTAREA'],
            ['name' => 'Radio Buttons', 'key_name' => 'RADIO'],
        ]);
    }

}