<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class EmailTemplateTableSeeder extends Seeder
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

        \DB::table('email_templates')->delete();

        \DB::statement('ALTER TABLE email_templates AUTO_INCREMENT = 1');

        $emailTemplate = new \App\Models\EmailTemplate();
        $emailTemplate->name = 'Welcome mail';
        $emailTemplate->subject = 'Welcome to leadify';
        $emailTemplate->content = <<<FOD
    <p>Hi&nbsp;##First Name##,</p><p>Thanks for purchasing leadify.</p><p>Thanks</p><p>##Company Name##<br></p>
FOD;

        $emailTemplate->shareable = 1;
        $emailTemplate->created_by = \App\Models\User::first()->id;
        $emailTemplate->save();

        \DB::commit();
    }
}
