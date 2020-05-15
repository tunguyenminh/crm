<?php

use App\Models\User;
use App\Models\SalesMember;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\App;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        \DB::beginTransaction();

        \DB::table('users')->delete();
        \DB::table('sales_members')->delete();

        \DB::statement('ALTER TABLE users AUTO_INCREMENT = 1');
        \DB::statement('ALTER TABLE sales_members AUTO_INCREMENT = 1');

        User::create([
            'first_name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('123456')
        ]);


        // Create three User instances...
        if(!App::environment('envato'))
        {
            User::create([
                'first_name' => 'Member',
                'email' => 'member@example.com',
                'password' => Hash::make('123456')
            ]);

            User::create([
                'first_name' => 'Manager',
                'email' => 'manager@example.com',
                'password' => Hash::make('123456')
            ]);

            $users = factory(User::class, 16)->create();

            // Sales Members
            $salesMember = factory(SalesMember::class, 30)->create();
        }

        // For adding twilio username
        $allUsers = \App\Models\User::select('id', 'first_name', 'last_name')->get();
        foreach ($allUsers as $allUser)
        {
            $name = trim(strtolower($allUser->first_name));

            if($allUser->last_name != '')
            {
                $name .= ' '. trim(strtolower($allUser->last_name));
            }

            $name = str_replace(' ', '_', $name);

            $checkIfNameAlreadyExists = \App\Models\User::where('twilio_client_name', $name)->count();

            if($checkIfNameAlreadyExists > 0)
            {
                $name = $name.'_'.$allUser->id;
            }

            $allUser->twilio_client_name = $name;
            $allUser->save();
        }

        \DB::commit();
    }
}
