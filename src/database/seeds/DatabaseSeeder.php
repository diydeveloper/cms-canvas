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

        $this->call(CmsCanvas\Database\Seeds\SettingsTableSeeder::class);
        $this->call(CmsCanvas\Database\Seeds\LanguagesTableSeeder::class);
        $this->call(CmsCanvas\Database\Seeds\TimezonesTableSeeder::class);
        $this->call(CmsCanvas\Database\Seeds\EntryStatusesTableSeeder::class);
        $this->call(CmsCanvas\Database\Seeds\ContentTypeFieldTypesTableSeeder::class);
    }

}
