<?php

namespace App\Http\Controllers\Admin;

use App\Classes\Common;
use App\Classes\Reply;
use App\Http\Requests\Admin\Profile\StoreRequest;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ProfileSettingController extends AdminBaseController
{
    /**
     * UserController constructor.
     */

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = trans('module_settings.profileSettings');
        $this->pageIcon = 'fa fa-user';
        $this->settingMenuActive = 'active';
        $this->profileSettingsActive = 'active';
    }

    public function index()
    {
        $this->timezones = \DateTimeZone::listIdentifiers(\DateTimeZone::ALL);
        $this->dateObject = Carbon::now();

        return view('admin.settings.profile.edit', $this->data);
    }

    public function store(StoreRequest $request)
    {
        \DB::beginTransaction();

        $user         = $this->user;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->skype_id = $request->skype_id;
        $user->contact_number = $request->contact_number;
        $user->address = $request->address;
        $user->country = $request->country;
        $user->zip_code = $request->zip_code;

        if($request->password != '')
        {
            $user->password = Hash::make($request->password);
        }

        $user->timezone = $request->timezone;
        $user->date_format = $request->date_format;
        $user->time_format = $request->time_format;

        switch ($user->date_format) {
            case 'd-m-Y':
                $user->date_picker_format = 'dd-mm-yyyy';
                break;
            case 'm-d-Y':
                $user->date_picker_format = 'mm-dd-yyyy';
                break;
            case 'Y-m-d':
                $user->date_picker_format = 'yyyy-mm-dd';
                break;
            case 'd.m.Y':
                $user->date_picker_format = 'dd.mm.yyyy';
                break;
            case 'm.d.Y':
                $user->date_picker_format = 'mm.dd.yyyy';
                break;
            case 'Y.m.d':
                $user->date_picker_format = 'yyyy.mm.dd';
                break;
            case 'd/m/Y':
                $user->date_picker_format = 'dd/mm/yyyy';
                break;
            case 'm/d/Y':
                $user->date_picker_format = 'mm/dd/yyyy';
                break;
            case 'Y/m/d':
                $user->date_picker_format = 'yyyy/mm/dd';
                break;
            case 'd-M-Y':
                $user->date_picker_format = 'dd-M-yyyy';
                break;
            case 'd/M/Y':
                $user->date_picker_format = 'dd/M/yyyy';
                break;
            case 'd.M.Y':
                $user->date_picker_format = 'dd.M.yyyy';
                break;
            case 'd M Y':
                $user->date_picker_format = 'dd M yyyy';
                break;
            case 'd F, Y':
                $user->date_picker_format = 'dd MM, yyyy';
                break;
            case 'D/M/Y':
                $user->date_picker_format = 'D/M/yyyy';
                break;
            case 'D.M.Y':
                $user->date_picker_format = 'D.M.yyyy';
                break;
            case 'D-M-Y':
                $user->date_picker_format = 'D-M-yyyy';
                break;
            case 'D M Y':
                $user->date_picker_format = 'D M yyyy';
                break;
            case 'd D M Y':
                $user->date_picker_format = 'dd D M yyyy';
                break;
            case 'D d M Y':
                $user->date_picker_format = 'D dd M yyyy';
                break;
            case 'dS M Y':
                $user->date_picker_format = 'dd M yyyy';
                break;

            default:
                $user->date_picker_format = 'mm/dd/yyyy';
                break;
        }

        //If Company Logo uploaded
        if($request->hasFile('image'))
        {
            $largeLogo  = $request->file('image');

            $fileName   = 'user_'.strtolower(Str::random(20)).'.'.$largeLogo->getClientOriginalExtension();
            $largeLogo->move($this->userImagePath, $fileName);

            //Deleting previous image
            $this->deleteLogoImage($user->image);

            $user->image        = $fileName;
        }
        $user->save();

        \DB::commit();
        return Reply::redirect(route('admin.settings.profile.index'), 'messages.updateSuccess');

    }

    protected function deleteLogoImage($imagePath)
    {
        if($imagePath != null) {
            if (File::exists($this->userImagePath . '/' . $imagePath))
            {
                Common::deleteCommonFiles($this->userImagePath . '/' . $imagePath);
            }
        }
    }

}
