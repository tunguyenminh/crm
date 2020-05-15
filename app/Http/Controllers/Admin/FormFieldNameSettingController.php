<?php

namespace App\Http\Controllers\Admin;

use App\Classes\Common;
use App\Classes\Reply;
use App\Http\Requests\Admin\FormFieldNameSetting\StoreRequest;

class FormFieldNameSettingController extends AdminBaseController
{
    /**
     * UserController constructor.
     */

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = trans('module_settings.formFieldsName');
        $this->pageIcon = 'fa fa-cogs';
        $this->settingMenuActive = 'active';
        $this->formFieldNameSettingsActive = 'active';
    }

    public function index()
    {
        return view('admin.settings.form-field-name.edit', $this->data);
    }

    public function store(StoreRequest $request)
    {
        $fieldNameArray = $this->formFieldNames;

        \DB::beginTransaction();

        $fieldNameArray->name = $request->name;
        $fieldNameArray->first_name = $request->first_name;
        $fieldNameArray->last_name = $request->last_name;
        $fieldNameArray->email = $request->email;
        $fieldNameArray->phone = $request->phone;
        $fieldNameArray->save();

        \DB::commit();

        return Reply::success( 'messages.updateSuccess');

    }

}
