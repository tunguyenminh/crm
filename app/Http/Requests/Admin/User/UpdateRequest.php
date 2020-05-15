<?php

namespace App\Http\Requests\Admin\User;

use App\Http\Requests\AdminCoreRequest;

class UpdateRequest extends AdminCoreRequest
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
            'email' => 'required|unique:users,email,'.$this->route('user'),
            'first_name'  => 'required',
            'status'     => 'required'
        ];

        if($this->contact_number != '')
        {
            $rules['contact_number'] = 'numeric|unique:users,contact_number,'.$this->route('user');
        }

        if($this->password != '')
        {
            $rules['password'] = 'required|min:6';
        }
        return $rules;

    }

}
