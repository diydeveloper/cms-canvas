<?php 

namespace CmsCanvas\Database\Seeds;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use CmsCanvas\Models\Content\Type\Field;
use CmsCanvas\Models\Language;
use CmsCanvas\Models\Content\Entry;

class EntryDataTableSeeder extends Seeder {

    public function run()
    {
        DB::table('entry_data')->delete();

        $field = Field::where('short_tag', 'content')->first();
        $language = Language::where('default', 1)->first();
        $homePage = Entry::where('title', 'Home')->first();
        $pageNotFound = Entry::where('title', 'Page Not Found')->first();

        DB::table('entry_data')->insert([
            [
                'entry_id' => $homePage->id, 
                'content_type_field_id' => $field->id, 
                'content_type_field_short_tag' => $field->short_tag, 
                'language_id' => $language->id,
                'language_locale' => $language->locale,
                'data' => 
                    '<h1>Welcome</h1>'
                    . '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam dolor risus, maximus ultricies augue ut, sodales volutpat augue. Nulla facilisi. Integer ut luctus nunc. Quisque neque odio, varius ac massa ut, consectetur convallis odio. Fusce a egestas nunc. Curabitur viverra, eros ac rhoncus faucibus, lorem mauris vulputate ipsum, nec finibus mauris ligula a est. Duis finibus eleifend orci, eu rutrum ex aliquet vitae. Aliquam faucibus, tellus et ultricies molestie, libero orci dapibus metus, eget aliquam ante orci sit amet ex. Curabitur euismod, velit a hendrerit rutrum, magna quam feugiat sapien, in suscipit orci mi eget velit. Sed tincidunt vitae erat ut ullamcorper. Aenean luctus purus ac sem auctor, in viverra neque vehicula. Mauris mollis massa eget lacus iaculis, id posuere elit convallis. Nunc leo purus, tincidunt non nisi sollicitudin, sagittis tincidunt ante. Sed sit amet feugiat justo. Praesent et quam massa.</p>'
                    . '<p>Sed lobortis eleifend dapibus. Sed sodales luctus dui, varius maximus elit dapibus non. Aenean dignissim sed ante sed consequat. Maecenas sed dui sodales, semper dolor a, accumsan ligula. Proin lacus felis, dictum ac dolor quis, tempus rhoncus urna. Nunc semper auctor nibh, lobortis tristique ex vestibulum vulputate. Interdum et malesuada fames ac ante ipsum primis in faucibus. Donec ullamcorper dictum semper. Sed vestibulum eu tellus quis accumsan. Etiam et urna blandit, faucibus augue ac, malesuada nisi.0</p>'
                    . '<p>Fusce finibus ligula posuere, sagittis velit ac, rutrum tortor. Praesent augue sem, ultricies sed mauris eu, tempor vehicula elit. Nunc metus mauris, placerat nec viverra lacinia, ullamcorper ac justo. Nunc volutpat nisi lacus, sit amet rutrum tortor scelerisque tincidunt. Integer lobortis faucibus sem dignissim maximus. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Suspendisse sit amet quam consectetur, gravida nunc in, elementum odio. Aenean bibendum eleifend tortor, sit amet varius tellus. Donec at eros ullamcorper lacus finibus dictum non et magna. Donec luctus tincidunt sapien interdum elementum. Sed pharetra, lacus sed consequat luctus, arcu ex malesuada ligula, sed bibendum mi quam quis lorem. Nullam dignissim augue in urna consequat, vitae convallis erat placerat. Praesent vitae erat mattis odio consequat ornare. Vivamus pellentesque, ante eget posuere maximus, orci felis malesuada purus, a aliquam sem dolor non nulla.</p>',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'entry_id' => $pageNotFound->id, 
                'content_type_field_id' => $field->id, 
                'content_type_field_short_tag' => $field->short_tag, 
                'language_id' => $language->id,
                'language_locale' => $language->locale,
                'data' => 
                    '<h1>404 - Page Not Found</h1>'
                    . '<p>The requested URL {{ url_current() }} was not found.</p>',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }

}
