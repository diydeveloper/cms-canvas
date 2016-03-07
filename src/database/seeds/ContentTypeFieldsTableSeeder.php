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

        $page = ContentType::where('short_name', 'page')->first();
        $blogPost = ContentType::where('short_name', 'blog_post')->first();
        $ckEditor = FieldType::where('class_name', '\CmsCanvas\Content\Type\FieldType\Ckeditor')->first();

        DB::table('content_type_fields')->insert([
            [
                'content_type_id' => $page->id, 
                'content_type_field_type_id' => $ckEditor->id, 
                'label' => 'Content',
                'short_tag' => 'content',
                'translate' => '0',
                'required' => '0',
                'settings' => '{"inline_editable":"1"}',
                'sort' => '1',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'content_type_id' => $blogPost->id, 
                'content_type_field_type_id' => $ckEditor->id, 
                'label' => 'Content',
                'short_tag' => 'content',
                'translate' => '0',
                'required' => '1',
                'settings' => '{"inline_editable":"1"}',
                'sort' => '1',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'content_type_id' => $blogPost->id, 
                'content_type_field_type_id' => $ckEditor->id, 
                'label' => 'Content Extended',
                'short_tag' => 'content_extended',
                'translate' => '0',
                'required' => '0',
                'settings' => '{"inline_editable":"1"}',
                'sort' => '2',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }

}