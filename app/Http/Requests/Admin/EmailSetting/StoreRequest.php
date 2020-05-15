<?php

namespace App\Http\Requests\Admin\EmailSetting;

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
        $rules = [
            'mail_from_name'     => 'required',
            'mail_from_email'     => 'required|email'
        ];

        if($this->mail_driver == 'smtp')
        {
            $rules['mail_host'] = 'required';
            $rules['mail_port'] = 'required';
            $rules['mail_username'] = 'required';
            $rules['mail_password'] = 'required';
        }

        return $rules;
    }
}
