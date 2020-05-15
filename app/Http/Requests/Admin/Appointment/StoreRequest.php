<?php

namespace App\Http\Requests\Admin\Appointment;

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
            'appointment_time'  => 'required',
            'sales_member_id'     => 'required'
        ];

        return $rules;

    }

}
