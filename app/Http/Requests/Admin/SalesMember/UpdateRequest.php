<?php

namespace App\Http\Requests\Admin\SalesMember;

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
            'first_name'  => 'required',
            'email' => 'required|unique:sales_members,email,'.$this->route('sales_user')
        ];

        if($this->contact_number != '')
        {
            $rules['contact_number'] = 'numeric|unique:sales_members,contact_number,'.$this->route('sales_user');
        }

        return $rules;

    }

}
