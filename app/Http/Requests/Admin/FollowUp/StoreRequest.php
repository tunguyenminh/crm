<?php

namespace App\Http\Requests\Admin\FollowUp;

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
            'attempted_by'     => 'required',
            'callback_time'  => 'required'
        ];

        return $rules;

    }

}
