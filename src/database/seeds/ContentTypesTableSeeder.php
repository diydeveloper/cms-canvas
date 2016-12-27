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
                'title' => 'Blog Post', 
                'layout' => <<<EOD
{% if url_title is not empty %}
    {% set entry = entry_first(content_type='blog_post', year=year, month=month, day=day, url_title=url_title, no_results_abort=404) %}
    {{ entry.includeThemeMetadata() }}
    <h1 class="entry-title">{{ entry.title }}</h1>
    <div class="entry-meta">{{ entry.created_at|user_date('d M Y') }} | Posted By {{ entry.author() }}</div>
    {{ entry.content }}
    {{ entry.content_extended }}
{% else %}
    {% set entries = entries(content_type='blog_post', year=year, month=month, day=day, paginate=15, order_by="created_at", sort="desc") %}
    {% if entries is empty %}
        No results found.
    {% else %}
        {% for entry in entries %}
            <h2 class="entry-title"><a href="{{ entry.url() }}">{{ entry.title }}</a></h2>
            <div class="entry-meta">{{ entry.created_at|user_date('d M Y') }} | Posted By {{ entry.author() }}</div>
            {{ entry.content }}
            {% if not entry.isLast() %}<hr class="entry" />{% endif %}
        {% endfor %}
        <div class="pagination-container">
            {{ entries.links() }}
        </div>
    {% endif %}
{% endif %}
EOD
                , 'short_name' => 'blog_post',
                'theme_layout' => 'default',
                'url_title_flag' => 1,
                'route' => 'blog/{year?}/{month?}/{day?}/{url_title?}',
                'entry_uri_template' => 'blog/{{ created_at_local|date("Y/m/d") }}/{{ url_title }}',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }

}