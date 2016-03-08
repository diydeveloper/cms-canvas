<?php namespace CmsCanvas\Database\Seeds;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class ContentTypeFieldTypesTableSeeder extends Seeder {

    public function run()
    {
        DB::table('content_type_field_types')->delete();

        DB::table('content_type_field_types')->insert([
            ['name' => 'CKEditor', 'class_name' => '\CmsCanvas\Content\Type\FieldType\Ckeditor'],
            ['name' => 'Text', 'class_name' => '\CmsCanvas\Content\Type\FieldType\Text'],
            ['name' => 'Checkbox', 'class_name' => '\CmsCanvas\Content\Type\FieldType\Checkbox'],
            ['name' => 'Image', 'class_name' => '\CmsCanvas\Content\Type\FieldType\Image'],
            ['name' => 'Date', 'class_name' => '\CmsCanvas\Content\Type\FieldType\Date'],
            ['name' => 'Date Time', 'class_name' => '\CmsCanvas\Content\Type\FieldType\DateTime'],
            ['name' => 'Dropdown', 'class_name' => '\CmsCanvas\Content\Type\FieldType\Dropdown'],
            ['name' => 'Textarea', 'class_name' => '\CmsCanvas\Content\Type\FieldType\Textarea'],
            ['name' => 'Radio Buttons', 'class_name' => '\CmsCanvas\Content\Type\FieldType\Radio'],
        ]);
    }

}