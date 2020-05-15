<?php

namespace App\Http\Requests\Admin\Forms;

use App\Http\Requests\AdminCoreRequest;

class AddNewFieldRequest extends AdminCoreRequest
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
            'field_name'     => 'required'
        ];

        return $rules;
    }

}
