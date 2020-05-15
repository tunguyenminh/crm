<?php

namespace App\Http\Requests\Admin\CallSetting;

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
        $rules = [];

        if($this->has('twilio_enabled') && $this->twilio_enabled == 1)
        {
            $rules['twilio_account_sid'] = 'required';
            $rules['twilio_auth_token'] = 'required';
            $rules['twilio_application_sid'] = 'required';
        }

        return $rules;
    }
}
