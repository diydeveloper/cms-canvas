<?php 

namespace CmsCanvas\Database\Seeds;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use CmsCanvas\Models\Content\Type as ContentType;
use CmsCanvas\Models\Content\Entry\Status as EntryStatus;
use CmsCanvas\Models\User;

class EntriesTableSeeder extends Seeder {

    public function run()
    {
        DB::table('entries')->delete();

        $page = ContentType::where('short_name', 'page')->first();
        $blogPost = ContentType::where('short_name', 'blog_post')->first();
        $entryStatus = EntryStatus::where('key_name', 'PUBLISHED')->first();
        $author = User::where('email', 'admin@domain.com')->first();
    	$now = Carbon::now();

        DB::table('entries')->insert([
            [
                'content_type_id' => $page->id, 
                'entry_status_id' => $entryStatus->id, 
                'author_id' => $author->id, 
                'title' => 'Home', 
                'url_title' => null, 
                'route' => null, 
                'template_flag' => 0, 
                'created_at' => $now,
                'created_at_local' => $now,
                'updated_at' => $now,
                'updated_at_local' => $now,
            ],
            [
                'content_type_id' => $page->id, 
                'entry_status_id' => $entryStatus->id, 
                'author_id' => $author->id, 
                'title' => 'Page Not Found', 
                'url_title' => null, 
                'route' => null, 
                'template_flag' => 1, 
                'created_at' => $now,
                'created_at_local' => $now,
                'updated_at' => $now,
                'updated_at_local' => $now,
            ],
            [
                'content_type_id' => $blogPost->id, 
                'entry_status_id' => $entryStatus->id, 
                'author_id' => $author->id, 
                'title' => 'Blog Post 1', 
                'url_title' => 'blog-post-1', 
                'route' => null, 
                'template_flag' => 1, 
                'created_at' => $now,
                'created_at_local' => $now,
                'updated_at' => $now,
                'updated_at_local' => $now,
            ],
            [
                'content_type_id' => $blogPost->id, 
                'entry_status_id' => $entryStatus->id, 
                'author_id' => $author->id, 
                'title' => 'Blog Post 2', 
                'url_title' => 'blog-post-2', 
                'route' => null, 
                'template_flag' => 1, 
                'created_at' => $now,
                'created_at_local' => $now,
                'updated_at' => $now,
                'updated_at_local' => $now,
            ],
        ]);
    }

}
