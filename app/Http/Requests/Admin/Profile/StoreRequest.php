<?php

namespace App\Http\Requests\Admin\Profile;

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
            'first_name'     => 'required',
            'image'          => 'image|max:2048'
        ];

        if($this->password != '')
        {
            $rules['password'] = 'required|min:6';
        }

        return $rules;
    }
}
