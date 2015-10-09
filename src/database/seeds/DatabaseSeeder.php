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

        $this->call(\CmsCanvas\Database\Seeds\LanguagesTableSeeder::class);
        $this->call(\CmsCanvas\Database\Seeds\TimezonesTableSeeder::class);
        $this->call(\CmsCanvas\Database\Seeds\PermissionsTableSeeder::class);
        $this->call(\CmsCanvas\Database\Seeds\RolesTableSeeder::class);
        $this->call(\CmsCanvas\Database\Seeds\RolePermissionsTableSeeder::class);
        $this->call(\CmsCanvas\Database\Seeds\RevisionResourceTypesTableSeeder::class);
        $this->call(\CmsCanvas\Database\Seeds\ContentTypesTableSeeder::class);
        $this->call(\CmsCanvas\Database\Seeds\ContentTypeFieldTypesTableSeeder::class);
        $this->call(\CmsCanvas\Database\Seeds\ContentTypeFieldsTableSeeder::class);
        $this->call(\CmsCanvas\Database\Seeds\EntryStatusesTableSeeder::class);
        $this->call(\CmsCanvas\Database\Seeds\EntriesTableSeeder::class);
        $this->call(\CmsCanvas\Database\Seeds\EntryDataTableSeeder::class);
        $this->call(\CmsCanvas\Database\Seeds\SettingsTableSeeder::class);
        $this->call(\CmsCanvas\Database\Seeds\UsersTableSeeder::class);
        $this->call(\CmsCanvas\Database\Seeds\UserRolesTableSeeder::class);
        $this->call(\CmsCanvas\Database\Seeds\NavigationsTableSeeder::class);
        $this->call(\CmsCanvas\Database\Seeds\NavigationItemsTableSeeder::class);
    }

}
