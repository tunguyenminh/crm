<?php

namespace App\Http\Requests\Admin\SalesMember;

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
            'email'    => 'required|email|unique:sales_members,email'
        ];

        if($this->contact_number != '')
        {
            $rules['contact_number'] = 'numeric|unique:sales_members,contact_number';
        }

        return $rules;
    }

}
