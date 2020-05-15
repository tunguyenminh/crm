<?php

namespace App\Http\Controllers\Admin;

use App\Classes\Reply;
use Froiden\Envato\Functions\EnvatoUpdate;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use ZanySoft\Zip\Zip;

class UpdateAppSettingController extends AdminBaseController
{
    /**
     * UserController constructor.
     */

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = trans('module_settings.updateApp');
        $this->pageIcon = 'fa fa-gift';
        $this->settingMenuActive = 'active';
        $this->updateAppSettingsActive = 'active';
    }

    public function index()
    {
        $updateVersionInfo = EnvatoUpdate::updateVersionInfo();
        if(isset($updateVersionInfo['lastVersion']))
        {
            $activeUpdateTab = 'oneClickUpdate';
        } else {
            $activeUpdateTab = 'appDetails';
        }
        $this->activeUpdateTab = $activeUpdateTab;

        // For Manual Update
        $client = new Client();
        $res = $client->request('GET', config('froiden_envato.updater_file_path'), ['verify' => false]);
        $lastVersion = $res->getBody();
        $lastVersion = json_decode($lastVersion, true);
        $this->downloadLink = config('froiden_envato.update_baseurl'). '/'. $lastVersion['archive'];
        $this->updateFilePath = config('froiden_envato.tmp_path');

        return view('admin.settings.update-app.index', $this->data);
    }

    public function store(Request $request)
    {
        $filename_tmp = config('froiden_envato.tmp_path');
        $file = $request->file('file');
        $file->move($filename_tmp, $file->getClientOriginalName());

        $this->updateFilePath = config('froiden_envato.tmp_path');
        $output = view('admin.settings.update-app.manual_files', $this->data)->render();

        return Reply::success('messages.dataFetchedSuccessfully', ['html' => $output]);
    }

    public function deleteFile(Request $request)
    {
        $filePath = $request->filePath;
        File::delete($filePath);
        return Reply::success('messages.fileDeleted');
    }

    public function installByFile(Request $request)
    {
        // Extracting
        $filePath = $request->filePath;
        $zip = Zip::open($filePath);
        // extract whole archive
        $zip->extract(base_path());

        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        Artisan::call('cache:clear');
        Session::flush();

        $status = Artisan::call('migrate:check');
        if ($status) {
            sleep(3);
            Artisan::call('migrate', array('--force' => true)); //migrate database
        }

        //logout user after installing update
        Auth::logout();
        return Reply::success('module_settings.installationSuccess');

    }

}
