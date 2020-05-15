<?php

namespace App\Http\Requests\Admin\User;

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
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'first_name'     => 'required',
            'status'     => 'required'
        ];

        if($this->contact_number != '')
        {
            $rules['contact_number'] = 'numeric|unique:users,contact_number';
        }

        return $rules;

    }

}
