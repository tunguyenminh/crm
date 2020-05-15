<?php

namespace App\Traits;

use App\Models\EmailSetting;
use Illuminate\Mail\MailServiceProvider;
use Illuminate\Support\Facades\Config;
use App\Models\Setting;

trait EmailSettings
{

    public function setMailConfigs()
    {
        $emailSetting = EmailSetting::first();
        $settings = Setting::first();

        if(config('app.env') !== 'development'){
            Config::set('mail.driver', $emailSetting->mail_driver);
            Config::set('mail.host', $emailSetting->mail_host);
            Config::set('mail.port', $emailSetting->mail_port);
            Config::set('mail.username', $emailSetting->mail_username);
            Config::set('mail.password', $emailSetting->mail_password);
            Config::set('mail.encryption', $emailSetting->mail_encryption);
        }

        Config::set('mail.from.name', $emailSetting->mail_from_name);
        Config::set('mail.from.address', $emailSetting->mail_from_email);

        Config::set('app.name', $settings->name);
        Config::set('app.logo', $settings->logo_url);

        (new MailServiceProvider(app()))->register();
    }

}