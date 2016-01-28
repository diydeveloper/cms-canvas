<?php 

namespace CmsCanvas\Database\Seeds;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use CmsCanvas\Models\Content\Type as ContentType;
use CmsCanvas\Models\Content\Entry\Status as EntryStatus;

class EntriesTableSeeder extends Seeder {

    public function run()
    {
        DB::table('entries')->delete();

        $contentType = ContentType::where('short_name', 'page')->first();
        $entryStatus = EntryStatus::where('key_name', 'PUBLISHED')->first();
	$now = Carbon::now();

        DB::table('entries')->insert([
            [
                'content_type_id' => $contentType->id, 
                'entry_status_id' => $entryStatus->id, 
                'title' => 'Home', 
                'route' => 'home', 
                'template_flag' => 0, 
                'created_at' => $now,
                'created_at_local' => $now,
                'updated_at' => $now,
                'updated_at_local' => $now,
            ],
            [
                'content_type_id' => $contentType->id, 
                'entry_status_id' => $entryStatus->id, 
                'title' => 'Page Not Found', 
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
