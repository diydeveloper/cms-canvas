<?php namespace CmsCanvas\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model as Eloquent;

class DatabaseSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Eloquent::unguard();

        $this->call('CmsCanvas\Database\Seeds\SettingsTableSeeder');
        $this->call('CmsCanvas\Database\Seeds\LanguagesTableSeeder');
        $this->call('CmsCanvas\Database\Seeds\TimezonesTableSeeder');
        $this->call('CmsCanvas\Database\Seeds\EntryStatusesTableSeeder');
        $this->call('CmsCanvas\Database\Seeds\ContentTypeFieldTypesTableSeeder');
    }

}
