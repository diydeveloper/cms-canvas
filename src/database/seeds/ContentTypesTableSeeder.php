<?php 

namespace CmsCanvas\Database\Seeds;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class ContentTypesTableSeeder extends Seeder {

    public function run()
    {
        DB::table('entries')->delete();
        DB::table('content_type_fields')->delete();
        DB::table('content_types')->delete();

        DB::table('content_types')->insert([
            [
                'title' => 'Page', 
                'layout' => '{{ content }}', 
                'short_name' => 'page',
                'max_revisions' => 5,
                'theme_layout' => 'default',
                'url_title_flag' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
        DB::table('content_types')->insert([
            [
                'title' => 'Blog', 
                'layout' => <<<EOD
{% if url_title is not empty %}
    {% set entry = entry(content_type='blog_entry', year=year, month=month, day=day, url_title=url_title, no_results_abort=404) %}
    {{ entry.setThemeMetadata() }}
    <h1>{{ entry.title }}</h1>
    {{ entry.content }}
    {{ entry.content_extended }}
{% else %}
    {% set entries = entries(content_type='blog_entry', year=year, month=month, day=day, no_results_abort=404, paginate=15) %}
    {% for entry in entries %}
        <h3><a href="{{ entry.url() }}">{{ entry.title }}</a></h3>
        <div>{{ entry.created_at|user_date('d M Y') }} | Posted By {{ entry.author() }}</div>
        {{ entry.content }}
        <hr />
    {% endfor %}
    {{ entries.links() }}
{% endif %}
EOD
                , 'short_name' => 'blog',
                'theme_layout' => 'default',
                'url_title_flag' => 1,
                'route' => 'blog/{year?}/{month?}/{day?}/{url_title?}',
                'entry_uri_template' => 'blog/{{ created_at|date("Y") }}/{{ created_at|date("m") }}/{{ created_at|date("d") }}/{{ url_title }}',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }

}