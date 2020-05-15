<?php

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run() {

        Model::unguard();

        \DB::beginTransaction();

        \DB::table('roles')->delete();

        \DB::statement('ALTER TABLE roles AUTO_INCREMENT = 1');

        $admin = new Role();
        $admin->name = 'admin';
        $admin->display_name = 'Admin'; // optional
        $admin->description = 'Admin is allowed to manage everything of the app.'; // optional
        $admin->save();

        // Admin Role
        $user = User::where('email', '=', 'admin@example.com')->first();
        $user->roles()->attach($admin->id); // id only

        if(!App::environment('envato'))
        {
            $member = new Role();
            $member->name = 'member';
            $member->display_name = 'Team Member';
            $member->description = 'Team Member can participate in campaigns which are assigned to him.';
            $member->save();

            // Team Member Role
            $user = User::where('email', '=', 'member@example.com')->first();
            $user->roles()->attach($member->id); // id only

            $manager = new Role();
            $manager->name = 'manager';
            $manager->display_name = 'Manager';
            $manager->description = 'Team Manager can full permissions to manage campaigns.';
            $manager->save();

            // Team Member Role
            $user = User::where('email', '=', 'manager@example.com')->first();
            $user->roles()->attach($manager->id); // id only
        }

        \DB::commit();
    }

}
