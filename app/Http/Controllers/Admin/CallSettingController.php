<?php

namespace App\Http\Controllers\Admin;

use App\Classes\Common;

use App\Http\Requests\Admin\CallSetting\StoreRequest;
use App\Models\Setting;
use App\Models\TwilioNumber;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Barryvdh\TranslationManager\Models\Translation;
use Illuminate\Support\Collection;

use App\Classes\Reply;
use App\Models\User;


class CallSettingController extends AdminBaseController
{
     /**
	 * UserController constructor.
	 */

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = trans('module_settings.callSettings');
        $this->pageIcon = 'fa fa-phone-volume';
        $this->settingMenuActive = 'active';
        $this->callSettingsActive = 'active';
    }

    public function index()
    {
        $this->editSetting = $this->settings;
        $this->twilioNumber = TwilioNumber::first();

        return view('admin.settings.call.twilio', $this->data);
    }

    public function store(StoreRequest $request)
    {
        \DB::beginTransaction();

        $setting         = $this->settings;
        $setting->twilio_enabled = $request->has('twilio_enabled') && $request->twilio_enabled == 1 ? 1 : 0;
        $setting->twilio_account_sid = $request->twilio_account_sid;
        $setting->twilio_auth_token = $request->twilio_auth_token;
        $setting->twilio_application_sid = $request->twilio_application_sid;
        $setting->save();

        \DB::commit();

        return Reply::redirect(route('admin.settings.calls.index'), 'messages.updateSuccess');

    }

    public function saveTwilioNumber(Request $request)
    {
        \DB::beginTransaction();

        $twilioNumber         = TwilioNumber::first();
        $twilioNumber->number = $request->number;
        $twilioNumber->inbound_recording = $request->inbound_recording;
        $twilioNumber->outbound_recording = $request->outbound_recording;
        $twilioNumber->save();

        \DB::commit();

        return Reply::success('messages.updateSuccess');
    }
}
