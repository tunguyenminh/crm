<?php

namespace App\Http\Controllers;

use App\Classes\Common;
use App\Models\FormFieldName;
use App\Models\Setting;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Froiden\Envato\Traits\AppBoot;

class MainBaseController extends Controller
{

    public function __construct()
    {
        parent::__construct();

        // Settings
        $this->settings = Setting::first();
        $this->year = Common::year();
        $this->bootstrapModalRight = true;
        $this->bootstrapModalSize = 'md';
        $this->siteLayout = $this->settings->app_layout; // top, sidebar
        $this->forbiddenErrorView = 'errors.403';
        $this->showFooter = true;
        $this->rtl = $this->settings->rtl;

        // Status
        $this->statusArray = [
            'enabled'  =>   __('app.enabled'),
            'disabled'  =>    __('app.disabled')
        ];

        // Setting assets path
        $allPaths = Common::getFolderPath();
        foreach($allPaths as $allPathKey => $allPath)
        {
            $this->{$allPathKey} = $allPath;
            $this->generateFolder($allPath);
        }

        $this->defaultFormFields = [
            'First Name',
            'Last Name',
            'Email',
            'Address',
            'Company',
            'Telephone No',
            'Postal Code',
            'Notes',
            'Website',
        ];

        App::setLocale($this->settings->locale);

        if (config('app.env') !== 'development') {
            config(['app.debug' => $this->settings->app_debug]);
        }

        $formFieldNames = FormFieldName::first();

        if($formFieldNames)
        {
            $this->nameArray = array_map('trim', explode(',', $formFieldNames->name));
            $this->firstNameArray = array_map('trim', explode(',', $formFieldNames->first_name));
            $this->lastNameArray = array_map('trim', explode(',', $formFieldNames->last_name));
            $this->emailArray = array_map('trim', explode(',', $formFieldNames->email));
            $this->phoneArray = array_map('trim', explode(',', $formFieldNames->phone));
        }
        $this->formFieldNames = $formFieldNames;

        $this->recordingOptions = [
            'do-not-record' => 'Do Not Record',
            'record-from-answer' => 'Record From Answer',
            'record-from-ringing' => 'Record From Ringing',
            'record-from-answer-dual' => 'Record From Answer Dual',
            'record-from-ringing-dual' => 'Record From Ringing Dual',
        ];
    }

    private function generateFolder($path)
    {
        if (!(\File::exists($path)))
        {
            \File::makeDirectory($path,  0775, true);
        }
    }
}
