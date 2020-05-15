<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class SettingsTableSeeder extends Seeder
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

        \DB::table('settings')->delete();
        \DB::table('email_settings')->delete();

        \DB::statement('ALTER TABLE settings AUTO_INCREMENT = 1');
        \DB::statement('ALTER TABLE email_settings AUTO_INCREMENT = 1');

        $setting = new \App\Models\Setting();
        $setting->name = 'Leadify';
        $setting->short_name = 'Leadify';
        $setting->email = 'myexamportal@gmail.com';
        $setting->phone = '+91 8741 004 005';
        $setting->address = '7 bajrang vihar, chapola ki dhani, snaganer jaipur, 302029';
        $setting->save();

        $emailSetting = new \App\Models\EmailSetting();
        $emailSetting->save();

        \DB::commit();
    }
}
