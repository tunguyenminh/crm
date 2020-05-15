<?php

namespace App\Http\Controllers\Admin;

use App\Classes\Common;
use App\Classes\Reply;
use App\Http\Requests\Admin\EmailSetting\StoreRequest;
use App\Models\EmailSetting;
use App\Models\Setting;
use App\Notifications\TestEmail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class EmailSettingController extends AdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = trans('module_settings.emailSettings');
        $this->pageIcon = 'fa fa-envelope';
        $this->settingMenuActive = 'active';
        $this->emailSettingsActive = 'active';
        $this->bootstrapModalRight = false;
        $this->bootstrapModalSize = 'md';
    }

    public function index()
    {
        $this->editSetting = EmailSetting::first();

        return view('admin.settings.email.edit', $this->data);
    }

    public function store(StoreRequest $request)
    {
        $setting         = EmailSetting::first();
        $setting->mail_driver = $request->mail_driver;
        $setting->mail_from_name = $request->mail_from_name;
        $setting->mail_from_email = $request->mail_from_email;

        if($request->mail_driver == 'smtp')
        {
            $setting->mail_host = $request->mail_host;
            $setting->mail_port = $request->mail_port;
            $setting->mail_username = $request->mail_username;
            $setting->mail_password = $request->mail_password;
            $setting->mail_encryption = $request->mail_encryption == 'null' ? null : $request->mail_encryption;
            $setting->save();

            $smtpResponse = $setting->verifySmtp();

            if($smtpResponse['success'])
            {
                return Reply::success($smtpResponse['message']);
            }

            // GMAIL SMTP ERROR
            $message = __('messages.smtpError').'<br><br> ';

            if ($setting->mail_host == 'smtp.gmail.com')
            {
                $secureUrl = 'https://myaccount.google.com/lesssecureapps';
                $message .= __('messages.smtpSecureEnabled');
                $message .= '<a  class="font-13" target="_blank" href="' . $secureUrl . '">' . $secureUrl . '</a>';
                $message .= '<hr>' . $smtpResponse['message'];
                return Reply::error($message);
            }
            return Reply::error($message . '<hr>' . $smtpResponse['message']);
        }

        $setting->save();

        return Reply::redirect(route('admin.settings.email.index'), 'messages.updateSuccess');

    }

    protected function deleteLogoImage($imagePath)
    {
        if($imagePath != null) {
            if (File::exists($this->companyLogoPath . '/' . $imagePath))
            {
                Common::deleteCommonFiles($this->companyLogoPath . '/' . $imagePath);
            }
        }
    }

    public function getSendMailModal()
    {
        $this->icon = 'edit';

        // Call the same create view for edit
        return view('admin.settings.email.test-mail', $this->data);
    }

    public function sendTestEmail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        $setting         = EmailSetting::first();
        $smtpResponse = $setting->verifySmtp();

        if ($smtpResponse['success']) {
            Notification::route('mail', $request->test_email)->notify(new TestEmail());
            return Reply::success('Test mail sent successfully');
        }
        return Reply::error($smtpResponse['message']);
    }
}
