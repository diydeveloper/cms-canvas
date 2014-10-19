<?php namespace CmsCanvas\Database\Seeds;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class ContentTypeFieldTypesTableSeeder extends Seeder {

    public function run()
    {
        DB::table('content_type_field_types')->delete();

        DB::table('content_type_field_types')->insert(array(
            array('name' => 'CKEditor', 'key_name' => 'CKEDITOR'),
            array('name' => 'Text', 'key_name' => 'TEXT'),
            array('name' => 'Checkbox', 'key_name' => 'CHECKBOX'),
            array('name' => 'Image', 'key_name' => 'IMAGE'),
        ));
    }

}