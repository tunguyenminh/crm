<?php

namespace App\Http\Controllers\Admin;

use App\Classes\Common;
use App\Classes\Reply;
use App\Http\Requests\Admin\AppSetting\StoreRequest;
use App\Models\Setting;
use Barryvdh\TranslationManager\Models\Translation;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CompanySettingController extends AdminBaseController
{
    /**
     * UserController constructor.
     */

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = trans('module_settings.companySettings');
        $this->pageIcon = 'fa fa-cogs';
        $this->settingMenuActive = 'active';
        $this->companySettingsActive = 'active';
    }

    public function index()
    {
        $this->editSetting = $this->settings;
        $allLangs = array_merge([config('app.locale')],
            Translation::groupBy('locale')->pluck('locale')->toArray());

        $allLangs = array_unique($allLangs);
        sort($allLangs);

        $this->allLangs = $allLangs;
        return view('admin.settings.company.edit', $this->data);
    }

    public function store(StoreRequest $request)
    {
        \DB::beginTransaction();

        $setting         = Setting::first();
        $setting->name = $request->name;
        $setting->short_name = $request->short_name;
        $setting->email = $request->email;
        $setting->phone = $request->phone;
        $setting->address = $request->address;
        $setting->locale = $request->lang;
        $setting->app_layout = $request->app_layout;
        $setting->app_update = $request->has('app_update') && $request->app_update == 1 ? 1 : 0;
        $setting->app_debug = $request->has('app_debug') && $request->app_debug == 1 ? 1 : 0;
        $setting->rtl = $request->has('rtl') && $request->rtl == 1 ? 1 : 0;

        //If Company Logo uploaded
        if($request->hasFile('logo'))
        {
            $largeLogo  = $request->file('logo');

            //Deleting previous logo
            $this->deleteLogoImage($setting->logo);

            $fileName   = Str::snake(strtolower($setting->short_name)).'.'.$largeLogo->getClientOriginalExtension();
            $largeLogo->move($this->companyLogoPath, $fileName);

            $setting->logo        = $fileName;
        }
        $setting->save();

        \DB::commit();
        return Reply::redirect(route('admin.settings.company.index'), 'messages.updateSuccess');

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

}
