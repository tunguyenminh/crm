<?php

namespace App\Http\Requests\Admin\FormFieldNameSetting;

use App\Http\Requests\AdminCoreRequest;

class StoreRequest extends AdminCoreRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'     => 'required',
            'first_name'     => 'required',
            'last_name'     => 'required',
            'email'     => 'required',
            'phone'     => 'required',
        ];
    }
}
