<?php 

namespace CmsCanvas\Database\Seeds;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use CmsCanvas\Models\Content\Type as ContentType;
use CmsCanvas\Models\Content\Type\Field\Type as FieldType;

class ContentTypeFieldsTableSeeder extends Seeder {

    public function run()
    {
        DB::table('content_type_fields')->delete();

        $contentType = ContentType::where('short_name', 'page')->first();
        $fieldType = FieldType::where('key_name', 'CKEDITOR')->first();

        DB::table('content_type_fields')->insert([
            [
                'content_type_id' => $contentType->id, 
                'content_type_field_type_id' => $fieldType->id, 
                'label' => 'Content',
                'short_tag' => 'content',
                'translate' => '0',
                'required' => '0',
                'settings' => '{"height":"","inline_editing":"1"}',
                'sort' => '1',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }

}