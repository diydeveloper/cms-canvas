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

        $pageContent = Field::where('short_tag', 'content')->whereHas('contentType', function ($query) {
            $query->where('short_name', 'page');
        })->first();
        $blogContent = Field::where('short_tag', 'content')->whereHas('contentType', function ($query) {
            $query->where('short_name', 'blog_post');
        })->first();
        $blogContentExtended = Field::where('short_tag', 'content_extended')->whereHas('contentType', function ($query) {
            $query->where('short_name', 'blog_post');
        })->first();
        $language = Language::where('default', 1)->first();
        $homePage = Entry::where('title', 'Home')->first();
        $pageNotFound = Entry::where('title', 'Page Not Found')->first();
        $blogPost1 = Entry::where('title', 'Blog Post 1')->first();
        $blogPost2 = Entry::where('title', 'Blog Post 2')->first();

        DB::table('entry_data')->insert([
            [
                'entry_id' => $homePage->id, 
                'content_type_field_id' => $pageContent->id, 
                'content_type_field_short_tag' => $pageContent->short_tag, 
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
                'content_type_field_id' => $pageContent->id, 
                'content_type_field_short_tag' => $pageContent->short_tag, 
                'language_locale' => $language->locale,
                'data' => 
                    '<h1>404 - Page Not Found</h1>'
                    . '<p>The requested URL {{ url_current() }} was not found.</p>',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'entry_id' => $blogPost1->id, 
                'content_type_field_id' => $blogContent->id, 
                'content_type_field_short_tag' => $blogContent->short_tag, 
                'language_locale' => $language->locale,
                'data' => 
                    '<p>Lorem ipsum dolor sit amet, eam ea utinam oporteat perfecto, habemus nominati sit in. His at ornatus legimus referrentur, qui doming sapientem no. His reque summo ut, has summo laoreet copiosae ne. Choro denique iudicabit in qui, quaeque abhorreant mei ex. Eum id essent latine ornatus. Vix fierent appellantur ad, tempor molestie dissentias at quo.</p>',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'entry_id' => $blogPost1->id, 
                'content_type_field_id' => $blogContentExtended->id, 
                'content_type_field_short_tag' => $blogContentExtended->short_tag, 
                'language_locale' => $language->locale,
                'data' => 
                    '<p>Quis sale expetenda ea mei, debet reformidans cum ea. Ad eos nusquam verterem, has in aperiri suscipit persequeris. Odio expetenda eam eu, pro eu eros atqui honestatis. Sit id tempor quidam convenire, sed probo quando te, adhuc debitis fastidii cu eam. Ut has rebum detracto similique, sea ad autem vivendo praesent.</p>'
                    . '<p>Vis soluta postulant dissentiunt in, sonet viderer rationibus at quo. Erant altera assueverit quo ne. Eum ne elit dicant, eirmod eleifend dissentias sed ne, ad ullum iuvaret senserit ius. Assum nonumes nec cu, et alii laoreet alienum nec, has in zril quando. Cu timeam iracundia adversarium mei, vix stet ludus cu.</p>'
                    . '<p>Ad decore placerat consetetur has. Ex nostrud assentior adolescens vis, pri ex hinc porro ceteros. Nec et copiosae incorrupte, case perpetua sit eu. Dicat aliquip consulatu ea nec, mazim audiam appellantur at est. Has exerci volutpat id. Nullam invidunt periculis te sed, civibus consulatu est ei.</p>',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'entry_id' => $blogPost2->id, 
                'content_type_field_id' => $blogContent->id, 
                'content_type_field_short_tag' => $blogContent->short_tag, 
                'language_locale' => $language->locale,
                'data' => 
                    '<p>Pro cu iracundia omittantur, tempor electram similique ex has, debet aeterno tacimates te usu. At sensibus percipitur cotidieque vix, no per alia mucius voluptatum, est abhorreant necessitatibus at. In alia accusam sensibus eam, definiebas scripserit ei est. Augue paulo senserit quo et, officiis elaboraret dissentiunt ut est, equidem graecis imperdiet quo ut. Suas clita propriae has an, libris quaestio at vim. Vix ea docendi minimum mentitum, laboramus omittantur eos an, id perpetua adolescens vituperatoribus quo.</p>',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'entry_id' => $blogPost2->id, 
                'content_type_field_id' => $blogContentExtended->id, 
                'content_type_field_short_tag' => $blogContentExtended->short_tag, 
                'language_locale' => $language->locale,
                'data' => 
                    '<p>Et aliquam mediocrem euripidis quo. Pro purto euismod apeirian id, at est vitae verterem, vituperata reformidans ex eos. Ei ius labitur inciderint, noster aliquip ornatus an pri. Exerci deleniti delicatissimi cu vis, tale accusata atomorum vix ea, ad pro quas corpora. No dico deseruisse usu, alia option neglegentur in mei. Nec minim labitur cu. Menandri sapientem interpretaris mei eu, eum iudico deseruisse te.</p>'
                    . '<p>Duo ei audire insolens gloriatur, qui cu audiam accumsan salutandi. Cu nonumy iracundia mel, possim audiam repudiandae ius et. Cetero oporteat assueverit quo cu, ex his purto dictas, et mel definiebas liberavisse. No graecis delectus intellegebat vis. Duo case aliquam petentium at, ad quo legere persius, an eam quas graece. Sit malorum signiferumque te. Vix cu tation quidam disputationi, saperet legendos vim ex, mel cu modo mutat philosophia.</p>'
                    . '<p>Quis tibique senserit mea ne, his te esse fastidii dissentiunt. Amet minim oblique per ex, ut vis recusabo interesset. No enim ferri harum mea, meis iracundia his id, esse nusquam petentium eum ad. Sed te voluptua patrioque, agam falli efficiantur te sed.</p>',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }

}
