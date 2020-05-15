<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
         $this->call(UsersTableSeeder::class);
         $this->call(RolesTableSeeder::class);
         $this->call(PermissionTableSeeder::class);
         $this->call(SettingsTableSeeder::class);

        if(!App::environment('envato'))
        {
            $this->call(FormTableSeeder::class);
            $this->call(CampaignTableSeeder::class);
            $this->call(EmailTemplateTableSeeder::class);
        }

    }
}
